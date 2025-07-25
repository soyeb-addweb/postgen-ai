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
        
        if ($status_code !== 200) {
            $error_data = json_decode($response_body, true);
            $error_message = $error_data['error']['message'] ?? 'Unknown API error';
            throw new Exception("API error ($status_code): $error_message");
        }
        
        $data = json_decode($response_body, true);
        
        if (!$data) {
            throw new Exception('Invalid API response format');
        }
        
        return $this->extract_content_from_response($provider, $data);
    }
    
    /**
     * Get API endpoint
     */
    private function get_api_endpoint($provider) {
        switch ($provider) {
            case 'perplexity':
                return self::PERPLEXITY_ENDPOINT;
            case 'openai':
                return self::OPENAI_ENDPOINT;
            case 'anthropic':
                return self::ANTHROPIC_ENDPOINT;
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
                $base_headers['Authorization'] = 'Bearer ' . $api_key;
                break;
            case 'anthropic':
                $base_headers['x-api-key'] = $api_key;
                $base_headers['anthropic-version'] = '2023-06-01';
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
                
            default:
                return [];
        }
    }
    
    /**
     * Extract content from API response
     */
    private function extract_content_from_response($provider, $data) {
        switch ($provider) {
            case 'perplexity':
            case 'openai':
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
                    'gpt-4-turbo'
                ];
                
            case 'anthropic':
                return [
                    'claude-3-sonnet-20240229',
                    'claude-3-opus-20240229',
                    'claude-3-haiku-20240307'
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