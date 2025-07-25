<?php
/**
 * API Connector Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_API_Connector {
    
    /**
     * API endpoints
     */
    const PERPLEXITY_ENDPOINT = 'https://api.perplexity.ai/chat/completions';
    const OPENAI_ENDPOINT = 'https://api.openai.com/v1/chat/completions';
    const ANTHROPIC_ENDPOINT = 'https://api.anthropic.com/v1/messages';
    
    // Paid API Providers
    const COPYAI_ENDPOINT = 'https://api.copy.ai/v1/chat/completions';
    const WRITESONIC_ENDPOINT = 'https://api.writesonic.com/v2/business/content/chatsonic';
    const NEUROFLASH_ENDPOINT = 'https://api.neuroflash.com/v1/text/generate';
    const INK_ENDPOINT = 'https://app.inkforall.com/api/v1/content/generate';
    
    // Free API Providers
    const OPENROUTER_ENDPOINT = 'https://openrouter.ai/api/v1/chat/completions';
    const DEEPINFRA_ENDPOINT = 'https://api.deepinfra.com/v1/openai/chat/completions';
    const HUGGINGFACE_ENDPOINT = 'https://api-inference.huggingface.co/models/';
    const REPLICATE_ENDPOINT = 'https://api.replicate.com/v1/predictions';
    
    /**
     * Rate limiting
     */
    private $rate_limit_key = 'smartwriter_ai_rate_limit';
    private $max_requests_per_minute = 20;
    
    /**
     * Logger instance
     */
    private $logger;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->logger = new SmartWriter_Logger();
    }
    
    /**
     * Test API connection
     */
    public function test_connection($api_key = null, $provider = null) {
        $settings = get_option('smartwriter_ai_settings', []);
        $api_key = $api_key ?: $settings['api_key'];
        $provider = $provider ?: $settings['api_provider'];
        
        if (empty($api_key)) {
            return false;
        }
        
        $test_prompt = "Test connection. Respond with: Connection successful.";
        
        try {
            $response = $this->make_api_request($provider, $api_key, $test_prompt);
            
            if ($response && !empty($response['content'])) {
                $this->logger->log('success', 'API connection test successful', [
                    'provider' => $provider,
                    'response_length' => strlen($response['content'])
                ]);
                
                return [
                    'success' => true,
                    'models' => $this->get_available_models($provider)
                ];
            }
        } catch (Exception $e) {
            $this->logger->log('error', 'API connection test failed', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }
    
    /**
     * Generate content using AI
     */
    public function generate_content($prompt, $topic = null) {
        $settings = get_option('smartwriter_ai_settings', []);
        
        if (empty($settings['api_key'])) {
            $this->logger->log('error', 'API key not configured');
            return false;
        }
        
        // Check rate limiting
        if (!$this->check_rate_limit()) {
            $this->logger->log('warning', 'Rate limit exceeded');
            return false;
        }
        
        // Process prompt with placeholders
        $processed_prompt = $this->process_prompt_placeholders($prompt, $topic);
        
        try {
            $response = $this->make_api_request(
                $settings['api_provider'],
                $settings['api_key'],
                $processed_prompt,
                $settings['model'] ?? 'llama-3.1-sonar-small-128k-online'
            );
            
            if ($response) {
                $parsed_content = $this->parse_ai_response($response['content']);
                
                $this->logger->log('success', 'Content generated successfully', [
                    'topic' => $topic,
                    'provider' => $settings['api_provider'],
                    'model' => $settings['model'],
                    'content_length' => strlen($response['content'])
                ]);
                
                return $parsed_content;
            }
        } catch (Exception $e) {
            $this->logger->log('error', 'Content generation failed', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }
    
    /**
     * Make API request
     */
    private function make_api_request($provider, $api_key, $prompt, $model = null) {
        $endpoint = $this->get_api_endpoint($provider);
        $headers = $this->get_api_headers($provider, $api_key);
        $body = $this->get_api_body($provider, $prompt, $model);
        
        $response = wp_remote_post($endpoint, [
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 60,
            'data_format' => 'body'
        ]);
        
        if (is_wp_error($response)) {
            throw new Exception('API request failed: ' . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($status_code !== 200 && $status_code !== 201 && $status_code !== 202) {
            $error_data = json_decode($response_body, true);
            
            // Handle different error formats
            $error_message = 'Unknown API error';
            if (isset($error_data['error']['message'])) {
                $error_message = $error_data['error']['message'];
            } elseif (isset($error_data['error'])) {
                $error_message = is_string($error_data['error']) ? $error_data['error'] : json_encode($error_data['error']);
            } elseif (isset($error_data['message'])) {
                $error_message = $error_data['message'];
            } elseif (isset($error_data['detail'])) {
                $error_message = $error_data['detail'];
            }
            
            throw new Exception("API error ($status_code): $error_message");
        }
        
        $data = json_decode($response_body, true);
        
        if (!$data) {
            throw new Exception('Invalid API response format');
        }
        
        // Handle Replicate's async responses
        if ($provider === 'replicate' && isset($data['status']) && $data['status'] === 'starting') {
            $data = $this->poll_replicate_result($data['id'], $api_key);
        }
        
        return $this->extract_content_from_response($provider, $data);
    }
    
    /**
     * Get API endpoint
     */
    private function get_api_endpoint($provider) {
        $settings = get_option('smartwriter_ai_settings', []);
        
        switch ($provider) {
            case 'perplexity':
                return self::PERPLEXITY_ENDPOINT;
            case 'openai':
                return self::OPENAI_ENDPOINT;
            case 'anthropic':
                return self::ANTHROPIC_ENDPOINT;
                
            // Paid Providers
            case 'copyai':
                return self::COPYAI_ENDPOINT;
            case 'writesonic':
                return self::WRITESONIC_ENDPOINT;
            case 'neuroflash':
                return self::NEUROFLASH_ENDPOINT;
            case 'ink':
                return self::INK_ENDPOINT;
                
            // Free Providers
            case 'openrouter':
                return self::OPENROUTER_ENDPOINT;
            case 'deepinfra':
                return self::DEEPINFRA_ENDPOINT;
            case 'huggingface':
                $model = $settings['model'] ?? 'mistralai/Mistral-7B-Instruct-v0.1';
                return self::HUGGINGFACE_ENDPOINT . $model;
            case 'replicate':
                return self::REPLICATE_ENDPOINT;
                
            default:
                return self::PERPLEXITY_ENDPOINT;
        }
    }
    
    /**
     * Get API headers
     */
    private function get_api_headers($provider, $api_key) {
        $base_headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'SmartWriter-AI/1.0.0'
        ];
        
        switch ($provider) {
            case 'perplexity':
            case 'openai':
            case 'openrouter':
            case 'deepinfra':
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                break;
                
            case 'anthropic':
                $base_headers['x-api-key'] = $api_key;
                $base_headers['anthropic-version'] = '2023-06-01';
                break;
                
            case 'copyai':
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                $base_headers['Accept'] = 'application/json';
                break;
                
            case 'writesonic':
                $base_headers['X-API-KEY'] = $api_key;
                $base_headers['Accept'] = 'application/json';
                break;
                
            case 'neuroflash':
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                $base_headers['Accept'] = 'application/json';
                break;
                
            case 'ink':
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                $base_headers['Accept'] = 'application/json';
                break;
                
            case 'huggingface':
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                break;
                
            case 'replicate':
                $base_headers['Authorization'] = 'Token ' . $api_key;
                break;
        }
        
        return $base_headers;
    }
    
    /**
     * Get API request body
     */
    private function get_api_body($provider, $prompt, $model) {
        switch ($provider) {
            case 'perplexity':
                return [
                    'model' => $model ?: 'llama-3.1-sonar-small-128k-online',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional content writer. Always respond with well-structured, engaging content. If asked to format as JSON, ensure valid JSON format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'frequency_penalty' => 0.1,
                    'presence_penalty' => 0.1
                ];
                
            case 'openai':
                return [
                    'model' => $model ?: 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional content writer. Always respond with well-structured, engaging content. If asked to format as JSON, ensure valid JSON format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7
                ];
                
            case 'anthropic':
                return [
                    'model' => $model ?: 'claude-3-sonnet-20240229',
                    'max_tokens' => 2000,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ];
                
            // OpenAI-Compatible Providers
            case 'openrouter':
                return [
                    'model' => $model ?: 'mistralai/mistral-7b-instruct',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional content writer. Always respond with well-structured, engaging content. If asked to format as JSON, ensure valid JSON format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7
                ];
                
            case 'deepinfra':
                return [
                    'model' => $model ?: 'mistralai/Mistral-7B-Instruct-v0.1',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional content writer. Always respond with well-structured, engaging content. If asked to format as JSON, ensure valid JSON format.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7,
                    'stream' => false
                ];
                
            // Paid Providers with Custom APIs
            case 'copyai':
                return [
                    'model' => $model ?: 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.7
                ];
                
            case 'writesonic':
                return [
                    'enable_google_results' => true,
                    'enable_memory' => false,
                    'input_text' => $prompt,
                    'history_data' => []
                ];
                
            case 'neuroflash':
                return [
                    'text' => $prompt,
                    'language' => 'en',
                    'style' => 'professional',
                    'max_tokens' => 2000
                ];
                
            case 'ink':
                return [
                    'prompt' => $prompt,
                    'type' => 'blog_post',
                    'max_tokens' => 2000,
                    'temperature' => 0.7
                ];
                
            // Hugging Face
            case 'huggingface':
                return [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_length' => 2000,
                        'temperature' => 0.7,
                        'do_sample' => true,
                        'top_p' => 0.9
                    ]
                ];
                
            // Replicate
            case 'replicate':
                return [
                    'version' => $this->get_replicate_model_version($model),
                    'input' => [
                        'prompt' => $prompt,
                        'max_tokens' => 2000,
                        'temperature' => 0.7
                    ]
                ];
                
            default:
                return [];
        }
    }
    
    /**
     * Poll Replicate for result
     */
    private function poll_replicate_result($prediction_id, $api_key) {
        $max_attempts = 30; // 30 seconds max
        $attempt = 0;
        
        while ($attempt < $max_attempts) {
            sleep(1);
            $attempt++;
            
            $response = wp_remote_get("https://api.replicate.com/v1/predictions/{$prediction_id}", [
                'headers' => [
                    'Authorization' => 'Token ' . $api_key,
                    'Content-Type' => 'application/json'
                ],
                'timeout' => 10
            ]);
            
            if (is_wp_error($response)) {
                continue;
            }
            
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (isset($data['status'])) {
                if ($data['status'] === 'succeeded') {
                    return $data;
                } elseif ($data['status'] === 'failed') {
                    throw new Exception('Replicate prediction failed: ' . ($data['error'] ?? 'Unknown error'));
                }
            }
        }
        
        throw new Exception('Replicate prediction timed out');
    }
    
    /**
     * Get Replicate model version
     */
    private function get_replicate_model_version($model) {
        $replicate_models = [
            'mistral-7b' => 'f1974b92b13d324bbbc3a51fec82af8e78831a0a926b9ef68dd3ac4b49e18b4a3',
            'llama2-7b' => 'f1d50bb24186c52daae319ca8366e53debdaa9e0ae7ff976e918df752732ccc4',
            'codellama-7b' => 'ac808388e2e9d8ed35a5bf2eaa7d83f0ad53f9e3df31a42e4eb0a0c3249b3165'
        ];
        
        return $replicate_models[$model] ?? $replicate_models['mistral-7b'];
    }
    
    /**
     * Extract content from API response
     */
    private function extract_content_from_response($provider, $data) {
        switch ($provider) {
            case 'perplexity':
            case 'openai':
            case 'openrouter':
            case 'deepinfra':
            case 'copyai':
                if (isset($data['choices'][0]['message']['content'])) {
                    return [
                        'content' => $data['choices'][0]['message']['content'],
                        'usage' => $data['usage'] ?? null
                    ];
                }
                break;
                
            case 'anthropic':
                if (isset($data['content'][0]['text'])) {
                    return [
                        'content' => $data['content'][0]['text'],
                        'usage' => $data['usage'] ?? null
                    ];
                }
                break;
                
            case 'writesonic':
                if (isset($data['message'])) {
                    return [
                        'content' => $data['message'],
                        'usage' => null
                    ];
                }
                break;
                
            case 'neuroflash':
                if (isset($data['data']['text'])) {
                    return [
                        'content' => $data['data']['text'],
                        'usage' => null
                    ];
                }
                break;
                
            case 'ink':
                if (isset($data['result'])) {
                    return [
                        'content' => $data['result'],
                        'usage' => null
                    ];
                }
                break;
                
            case 'huggingface':
                if (isset($data[0]['generated_text'])) {
                    // Remove the original prompt from the response
                    $content = $data[0]['generated_text'];
                    return [
                        'content' => $content,
                        'usage' => null
                    ];
                } elseif (is_string($data)) {
                    return [
                        'content' => $data,
                        'usage' => null
                    ];
                }
                break;
                
            case 'replicate':
                if (isset($data['output'])) {
                    $content = is_array($data['output']) ? implode('', $data['output']) : $data['output'];
                    return [
                        'content' => $content,
                        'usage' => null
                    ];
                }
                break;
        }
        
        throw new Exception('Unable to extract content from API response');
    }
    
    /**
     * Process prompt placeholders
     */
    private function process_prompt_placeholders($prompt, $topic = null) {
        $placeholders = [
            '{topic}' => $topic ?: $this->generate_trending_topic(),
            '{date}' => current_time('F j, Y'),
            '{keyword}' => $topic ?: 'technology trends',
            '{category}' => $this->get_default_category_name(),
            '{author}' => wp_get_current_user()->display_name ?: 'SmartWriter AI'
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $prompt);
    }
    
    /**
     * Parse AI response
     */
    private function parse_ai_response($content) {
        // Try to parse as JSON first
        $json_data = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($json_data)) {
            // Valid JSON response
            return [
                'title' => $json_data['title'] ?? $this->extract_title_from_text($content),
                'content' => $json_data['content'] ?? $content,
                'meta_description' => $json_data['meta_description'] ?? $this->generate_meta_description($content),
                'tags' => $json_data['tags'] ?? $this->extract_tags_from_content($content),
                'focus_keyword' => $json_data['focus_keyword'] ?? $this->extract_focus_keyword($content),
                'category' => $json_data['category'] ?? null
            ];
        }
        
        // Parse as plain text
        return [
            'title' => $this->extract_title_from_text($content),
            'content' => $this->clean_content($content),
            'meta_description' => $this->generate_meta_description($content),
            'tags' => $this->extract_tags_from_content($content),
            'focus_keyword' => $this->extract_focus_keyword($content),
            'category' => null
        ];
    }
    
    /**
     * Extract title from text
     */
    private function extract_title_from_text($content) {
        // Look for title patterns
        $patterns = [
            '/^#\s*(.+)$/m',           // Markdown heading
            '/^Title:\s*(.+)$/mi',     // Title: format
            '/^(.+)$/m'                // First line
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $title = trim($matches[1]);
                if (strlen($title) > 10 && strlen($title) < 100) {
                    return $title;
                }
            }
        }
        
        // Fallback to first meaningful sentence
        $sentences = explode('.', $content);
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 20 && strlen($sentence) < 80) {
                return $sentence;
            }
        }
        
        return 'AI Generated Post - ' . current_time('M j, Y');
    }
    
    /**
     * Generate meta description
     */
    private function generate_meta_description($content) {
        $clean_content = wp_strip_all_tags($content);
        $sentences = explode('.', $clean_content);
        
        $description = '';
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($description . $sentence) < 150) {
                $description .= $sentence . '. ';
            } else {
                break;
            }
        }
        
        return trim($description) ?: substr($clean_content, 0, 155) . '...';
    }
    
    /**
     * Extract tags from content
     */
    private function extract_tags_from_content($content) {
        $tags = [];
        
        // Common tech/business keywords
        $keywords = [
            'AI', 'artificial intelligence', 'machine learning', 'technology', 'innovation',
            'business', 'marketing', 'digital', 'online', 'strategy', 'growth', 'trends',
            'development', 'software', 'automation', 'productivity', 'success'
        ];
        
        $content_lower = strtolower($content);
        
        foreach ($keywords as $keyword) {
            if (strpos($content_lower, strtolower($keyword)) !== false) {
                $tags[] = $keyword;
            }
        }
        
        return array_slice(array_unique($tags), 0, 5);
    }
    
    /**
     * Extract focus keyword
     */
    private function extract_focus_keyword($content) {
        $tags = $this->extract_tags_from_content($content);
        return !empty($tags) ? $tags[0] : 'technology';
    }
    
    /**
     * Clean content
     */
    private function clean_content($content) {
        // Remove title if it appears at the beginning
        $content = preg_replace('/^#\s*.+\n/m', '', $content);
        $content = preg_replace('/^Title:\s*.+\n/mi', '', $content);
        
        // Clean up extra whitespace
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        return trim($content);
    }
    
    /**
     * Generate trending topic
     */
    private function generate_trending_topic() {
        $topics = [
            'Latest Technology Trends in ' . date('Y'),
            'Digital Marketing Strategies for Small Businesses',
            'The Future of Remote Work',
            'Sustainable Business Practices',
            'Cybersecurity Best Practices',
            'Social Media Marketing Tips',
            'E-commerce Growth Strategies',
            'Productivity Tools and Techniques',
            'Customer Experience Optimization',
            'Data Analytics for Business Growth'
        ];
        
        return $topics[array_rand($topics)];
    }
    
    /**
     * Get default category name
     */
    private function get_default_category_name() {
        $settings = get_option('smartwriter_ai_settings', []);
        $category_id = $settings['default_category'] ?? 1;
        $category = get_category($category_id);
        
        return $category ? $category->name : 'Uncategorized';
    }
    
    /**
     * Check rate limiting
     */
    private function check_rate_limit() {
        $current_minute = floor(time() / 60);
        $rate_data = get_transient($this->rate_limit_key . '_' . $current_minute);
        
        if ($rate_data === false) {
            $rate_data = 0;
        }
        
        if ($rate_data >= $this->max_requests_per_minute) {
            return false;
        }
        
        set_transient($this->rate_limit_key . '_' . $current_minute, $rate_data + 1, 60);
        return true;
    }
    
    /**
     * Get available models for provider
     */
    private function get_available_models($provider) {
        switch ($provider) {
            case 'perplexity':
                return [
                    'llama-3.1-sonar-small-128k-online',
                    'llama-3.1-sonar-large-128k-online',
                    'llama-3.1-sonar-huge-128k-online'
                ];
                
            case 'openai':
                return [
                    'gpt-3.5-turbo',
                    'gpt-4',
                    'gpt-4-turbo',
                    'gpt-4o'
                ];
                
            case 'anthropic':
                return [
                    'claude-3-sonnet-20240229',
                    'claude-3-opus-20240229',
                    'claude-3-haiku-20240307',
                    'claude-3-5-sonnet-20241022'
                ];
                
            // Paid Providers
            case 'copyai':
                return [
                    'gpt-3.5-turbo',
                    'gpt-4',
                    'claude-3-sonnet'
                ];
                
            case 'writesonic':
                return [
                    'gpt-3.5-turbo',
                    'gpt-4',
                    'claude-3-sonnet'
                ];
                
            case 'neuroflash':
                return [
                    'neuroflash-standard',
                    'neuroflash-premium'
                ];
                
            case 'ink':
                return [
                    'ink-gpt-3.5',
                    'ink-gpt-4'
                ];
                
            // Free Providers
            case 'openrouter':
                return [
                    'mistralai/mistral-7b-instruct',
                    'microsoft/phi-3-mini',
                    'microsoft/phi-2',
                    'nousresearch/nous-capybara-7b',
                    'openchat/openchat-7b',
                    'gryphe/mythomist-7b'
                ];
                
            case 'deepinfra':
                return [
                    'mistralai/Mistral-7B-Instruct-v0.1',
                    'meta-llama/Llama-2-7b-chat-hf',
                    'meta-llama/Llama-2-13b-chat-hf',
                    'tiiuae/falcon-7b-instruct',
                    'codellama/CodeLlama-7b-Instruct-hf'
                ];
                
            case 'huggingface':
                return [
                    'mistralai/Mistral-7B-Instruct-v0.1',
                    'bigscience/bloom',
                    'tiiuae/falcon-7b',
                    'microsoft/DialoGPT-medium',
                    'EleutherAI/gpt-neo-2.7B'
                ];
                
            case 'replicate':
                return [
                    'mistral-7b',
                    'llama2-7b',
                    'codellama-7b'
                ];
                
            default:
                return [];
        }
    }
    
    /**
     * Get image for post
     */
    public function get_featured_image($topic, $title = '') {
        $settings = get_option('smartwriter_ai_settings', []);
        
        if (!$settings['generate_images'] || empty($settings['image_api_key'])) {
            return null;
        }
        
        switch ($settings['image_api']) {
            case 'unsplash':
                return $this->get_unsplash_image($topic, $settings['image_api_key']);
                
            case 'pexels':
                return $this->get_pexels_image($topic, $settings['image_api_key']);
                
            case 'dall-e':
                return $this->generate_dalle_image($title, $settings['image_api_key']);
                
            default:
                return null;
        }
    }
    
    /**
     * Get Unsplash image
     */
    private function get_unsplash_image($topic, $api_key) {
        $query = urlencode($topic);
        $url = "https://api.unsplash.com/search/photos?query={$query}&per_page=1&orientation=landscape";
        
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Client-ID ' . $api_key
            ],
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!empty($data['results'][0]['urls']['regular'])) {
            return [
                'url' => $data['results'][0]['urls']['regular'],
                'alt' => $data['results'][0]['alt_description'] ?? $topic,
                'credit' => $data['results'][0]['user']['name'] ?? ''
            ];
        }
        
        return null;
    }
    
    /**
     * Get Pexels image
     */
    private function get_pexels_image($topic, $api_key) {
        $query = urlencode($topic);
        $url = "https://api.pexels.com/v1/search?query={$query}&per_page=1&orientation=landscape";
        
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => $api_key
            ],
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!empty($data['photos'][0]['src']['large'])) {
            return [
                'url' => $data['photos'][0]['src']['large'],
                'alt' => $data['photos'][0]['alt'] ?? $topic,
                'credit' => $data['photos'][0]['photographer'] ?? ''
            ];
        }
        
        return null;
    }
    
    /**
     * Generate DALL-E image
     */
    private function generate_dalle_image($title, $api_key) {
        $prompt = "A professional, modern illustration representing: " . $title . ". Clean, minimalist style, suitable for a blog post header.";
        
        $response = wp_remote_post('https://api.openai.com/v1/images/generations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'model' => 'dall-e-3'
            ]),
            'timeout' => 60
        ]);
        
        if (is_wp_error($response)) {
            return null;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!empty($data['data'][0]['url'])) {
            return [
                'url' => $data['data'][0]['url'],
                'alt' => $title,
                'credit' => 'Generated by DALL-E'
            ];
        }
        
        return null;
    }
}