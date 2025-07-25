<?php
/**
 * Post Creator Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_Post_Creator {
    
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
     * Create WordPress post
     */
    public function create_post($content_data, $image_data = null, $schedule_date = null) {
        try {
            $settings = get_option('smartwriter_ai_settings', []);
            
            // Prepare post data
            $post_data = $this->prepare_post_data($content_data, $settings, $schedule_date);
            
            // Create the post
            $post_id = wp_insert_post($post_data, true);
            
            if (is_wp_error($post_id)) {
                throw new Exception('Failed to create post: ' . $post_id->get_error_message());
            }
            
            // Mark as AI generated
            update_post_meta($post_id, '_smartwriter_ai_generated', 1);
            update_post_meta($post_id, '_smartwriter_ai_version', SMARTWRITER_AI_VERSION);
            update_post_meta($post_id, '_smartwriter_ai_created_at', current_time('mysql'));
            
            // Set category
            $this->set_post_category($post_id, $content_data, $settings);
            
            // Set tags
            $this->set_post_tags($post_id, $content_data);
            
            // Set featured image
            if ($image_data) {
                $this->set_featured_image($post_id, $image_data);
            }
            
            // Set SEO metadata
            $this->set_seo_metadata($post_id, $content_data, $settings);
            
            // Add custom fields
            $this->add_custom_fields($post_id, $content_data);
            
            $this->logger->log('success', 'Post created successfully', [
                'post_id' => $post_id,
                'title' => $content_data['title'],
                'word_count' => str_word_count(wp_strip_all_tags($content_data['content'])),
                'has_image' => !empty($image_data),
                'schedule_date' => $schedule_date
            ]);
            
            return $post_id;
            
        } catch (Exception $e) {
            $this->logger->log('error', 'Failed to create post', [
                'error' => $e->getMessage(),
                'title' => $content_data['title'] ?? 'Unknown'
            ]);
            
            return false;
        }
    }
    
    /**
     * Prepare post data
     */
    private function prepare_post_data($content_data, $settings, $schedule_date = null) {
        $post_status = $settings['auto_publish'] ? 'publish' : 'draft';
        $post_date = $schedule_date ?: current_time('mysql');
        
        // Clean and format content
        $content = $this->format_post_content($content_data['content']);
        
        $post_data = [
            'post_title' => sanitize_text_field($content_data['title']),
            'post_content' => $content,
            'post_status' => $post_status,
            'post_type' => 'post',
            'post_author' => get_current_user_id() ?: 1,
            'post_date' => $post_date,
            'post_date_gmt' => get_gmt_from_date($post_date),
            'comment_status' => get_option('default_comment_status'),
            'ping_status' => get_option('default_ping_status')
        ];
        
        // Add excerpt if available
        if (!empty($content_data['meta_description'])) {
            $post_data['post_excerpt'] = sanitize_text_field($content_data['meta_description']);
        }
        
        return $post_data;
    }
    
    /**
     * Format post content
     */
    private function format_post_content($content) {
        // Clean the content
        $content = wp_kses_post($content);
        
        // Add paragraph tags if missing
        if (strpos($content, '<p>') === false) {
            $paragraphs = explode("\n\n", $content);
            $paragraphs = array_map('trim', $paragraphs);
            $paragraphs = array_filter($paragraphs);
            $content = '<p>' . implode('</p><p>', $paragraphs) . '</p>';
        }
        
        // Convert line breaks to HTML
        $content = wpautop($content);
        
        // Clean up extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);
        
        return trim($content);
    }
    
    /**
     * Set post category
     */
    private function set_post_category($post_id, $content_data, $settings) {
        $category_id = $settings['default_category'] ?? 1;
        
        // Check if content suggests a specific category
        if (!empty($content_data['category'])) {
            $suggested_category = $this->find_or_create_category($content_data['category']);
            if ($suggested_category) {
                $category_id = $suggested_category;
            }
        }
        
        wp_set_post_categories($post_id, [$category_id]);
        
        $this->logger->log('info', 'Post category set', [
            'post_id' => $post_id,
            'category_id' => $category_id
        ]);
    }
    
    /**
     * Find or create category
     */
    private function find_or_create_category($category_name) {
        $category = get_category_by_slug(sanitize_title($category_name));
        
        if (!$category) {
            $category = get_term_by('name', $category_name, 'category');
        }
        
        if (!$category) {
            $result = wp_insert_term($category_name, 'category');
            if (!is_wp_error($result)) {
                return $result['term_id'];
            }
        }
        
        return $category ? $category->term_id : null;
    }
    
    /**
     * Set post tags
     */
    private function set_post_tags($post_id, $content_data) {
        if (empty($content_data['tags'])) {
            return;
        }
        
        $tags = is_array($content_data['tags']) ? $content_data['tags'] : explode(',', $content_data['tags']);
        $tags = array_map('trim', $tags);
        $tags = array_filter($tags);
        
        wp_set_post_tags($post_id, $tags);
        
        $this->logger->log('info', 'Post tags set', [
            'post_id' => $post_id,
            'tags' => $tags
        ]);
    }
    
    /**
     * Set featured image
     */
    private function set_featured_image($post_id, $image_data) {
        try {
            $image_id = $this->upload_image_from_url($image_data['url'], $image_data['alt']);
            
            if ($image_id) {
                set_post_thumbnail($post_id, $image_id);
                
                // Add image credit if available
                if (!empty($image_data['credit'])) {
                    update_post_meta($image_id, '_image_credit', sanitize_text_field($image_data['credit']));
                }
                
                $this->logger->log('success', 'Featured image set', [
                    'post_id' => $post_id,
                    'image_id' => $image_id,
                    'image_url' => $image_data['url']
                ]);
                
                return $image_id;
            }
        } catch (Exception $e) {
            $this->logger->log('error', 'Failed to set featured image', [
                'post_id' => $post_id,
                'error' => $e->getMessage(),
                'image_url' => $image_data['url']
            ]);
        }
        
        return false;
    }
    
    /**
     * Upload image from URL
     */
    private function upload_image_from_url($image_url, $alt_text = '') {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        
        // Download image
        $temp_file = download_url($image_url);
        
        if (is_wp_error($temp_file)) {
            throw new Exception('Failed to download image: ' . $temp_file->get_error_message());
        }
        
        // Prepare file array
        $file = [
            'name' => basename($image_url),
            'type' => wp_check_filetype(basename($image_url))['type'],
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file)
        ];
        
        // Upload file
        $overrides = ['test_form' => false];
        $upload = wp_handle_sideload($file, $overrides);
        
        // Clean up temp file
        unlink($temp_file);
        
        if (!empty($upload['error'])) {
            throw new Exception('Failed to upload image: ' . $upload['error']);
        }
        
        // Create attachment
        $attachment = [
            'post_mime_type' => $upload['type'],
            'post_title' => $alt_text ?: sanitize_file_name(basename($upload['file'])),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        
        if (is_wp_error($attach_id)) {
            throw new Exception('Failed to create attachment: ' . $attach_id->get_error_message());
        }
        
        // Generate metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        // Set alt text
        if ($alt_text) {
            update_post_meta($attach_id, '_wp_attachment_image_alt', sanitize_text_field($alt_text));
        }
        
        return $attach_id;
    }
    
    /**
     * Set SEO metadata
     */
    private function set_seo_metadata($post_id, $content_data, $settings) {
        $seo_plugin = $settings['seo_plugin'] ?? 'yoast';
        
        switch ($seo_plugin) {
            case 'yoast':
                $this->set_yoast_seo_data($post_id, $content_data);
                break;
                
            case 'aioseo':
                $this->set_aioseo_data($post_id, $content_data);
                break;
                
            case 'rankmath':
                $this->set_rankmath_data($post_id, $content_data);
                break;
                
            default:
                // Set basic meta tags
                $this->set_basic_meta_data($post_id, $content_data);
                break;
        }
        
        $this->logger->log('info', 'SEO metadata set', [
            'post_id' => $post_id,
            'seo_plugin' => $seo_plugin,
            'meta_description_length' => strlen($content_data['meta_description'] ?? ''),
            'focus_keyword' => $content_data['focus_keyword'] ?? ''
        ]);
    }
    
    /**
     * Set Yoast SEO data
     */
    private function set_yoast_seo_data($post_id, $content_data) {
        if (!empty($content_data['title'])) {
            update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($content_data['title']));
        }
        
        if (!empty($content_data['meta_description'])) {
            update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_text_field($content_data['meta_description']));
        }
        
        if (!empty($content_data['focus_keyword'])) {
            update_post_meta($post_id, '_yoast_wpseo_focuskw', sanitize_text_field($content_data['focus_keyword']));
            update_post_meta($post_id, '_yoast_wpseo_focuskeywords', json_encode([
                ['keyword' => sanitize_text_field($content_data['focus_keyword']), 'score' => 75]
            ]));
        }
        
        // Set content score
        update_post_meta($post_id, '_yoast_wpseo_content_score', 75);
        
        // Set SEO score
        update_post_meta($post_id, '_yoast_wpseo_linkdex', 75);
    }
    
    /**
     * Set All in One SEO data
     */
    private function set_aioseo_data($post_id, $content_data) {
        global $wpdb;
        
        $aioseo_table = $wpdb->prefix . 'aioseo_posts';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$aioseo_table'") !== $aioseo_table) {
            return;
        }
        
        $aioseo_data = [
            'post_id' => $post_id,
            'title' => !empty($content_data['title']) ? sanitize_text_field($content_data['title']) : null,
            'description' => !empty($content_data['meta_description']) ? sanitize_text_field($content_data['meta_description']) : null,
            'keywords' => !empty($content_data['focus_keyword']) ? sanitize_text_field($content_data['focus_keyword']) : null,
            'created' => current_time('mysql'),
            'updated' => current_time('mysql')
        ];
        
        $wpdb->replace($aioseo_table, $aioseo_data);
    }
    
    /**
     * Set RankMath data
     */
    private function set_rankmath_data($post_id, $content_data) {
        if (!empty($content_data['title'])) {
            update_post_meta($post_id, 'rank_math_title', sanitize_text_field($content_data['title']));
        }
        
        if (!empty($content_data['meta_description'])) {
            update_post_meta($post_id, 'rank_math_description', sanitize_text_field($content_data['meta_description']));
        }
        
        if (!empty($content_data['focus_keyword'])) {
            update_post_meta($post_id, 'rank_math_focus_keyword', sanitize_text_field($content_data['focus_keyword']));
        }
        
        // Set pillar content flag
        update_post_meta($post_id, 'rank_math_pillar_content', 'off');
        
        // Set robots meta
        update_post_meta($post_id, 'rank_math_robots', ['index', 'follow']);
    }
    
    /**
     * Set basic meta data (fallback)
     */
    private function set_basic_meta_data($post_id, $content_data) {
        if (!empty($content_data['meta_description'])) {
            update_post_meta($post_id, '_meta_description', sanitize_text_field($content_data['meta_description']));
        }
        
        if (!empty($content_data['focus_keyword'])) {
            update_post_meta($post_id, '_focus_keyword', sanitize_text_field($content_data['focus_keyword']));
        }
    }
    
    /**
     * Add custom fields
     */
    private function add_custom_fields($post_id, $content_data) {
        // Store original AI response
        update_post_meta($post_id, '_smartwriter_original_data', $content_data);
        
        // Content analytics
        $content_stats = [
            'word_count' => str_word_count(wp_strip_all_tags($content_data['content'])),
            'character_count' => strlen($content_data['content']),
            'paragraph_count' => substr_count($content_data['content'], '<p>'),
            'readability_score' => $this->calculate_readability_score($content_data['content'])
        ];
        
        update_post_meta($post_id, '_smartwriter_content_stats', $content_stats);
        
        // Social media meta
        if (!empty($content_data['meta_description'])) {
            update_post_meta($post_id, '_smartwriter_social_description', $content_data['meta_description']);
        }
        
        if (!empty($content_data['title'])) {
            update_post_meta($post_id, '_smartwriter_social_title', $content_data['title']);
        }
    }
    
    /**
     * Calculate basic readability score
     */
    private function calculate_readability_score($content) {
        $text = wp_strip_all_tags($content);
        $sentences = explode('.', $text);
        $words = str_word_count($text);
        $syllables = $this->count_syllables($text);
        
        if (count($sentences) == 0 || $words == 0) {
            return 0;
        }
        
        // Flesch Reading Ease formula (simplified)
        $avg_sentence_length = $words / count($sentences);
        $avg_syllables_per_word = $syllables / $words;
        
        $score = 206.835 - (1.015 * $avg_sentence_length) - (84.6 * $avg_syllables_per_word);
        
        return max(0, min(100, round($score)));
    }
    
    /**
     * Count syllables (simplified)
     */
    private function count_syllables($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z\s]/', '', $text);
        $words = explode(' ', $text);
        $syllable_count = 0;
        
        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            
            // Simple syllable counting
            $vowel_count = preg_match_all('/[aeiou]/', $word);
            $syllable_count += max(1, $vowel_count);
        }
        
        return $syllable_count;
    }
    
    /**
     * Update existing post
     */
    public function update_post($post_id, $content_data, $image_data = null) {
        try {
            $settings = get_option('smartwriter_ai_settings', []);
            
            // Prepare update data
            $post_data = [
                'ID' => $post_id,
                'post_title' => sanitize_text_field($content_data['title']),
                'post_content' => $this->format_post_content($content_data['content'])
            ];
            
            if (!empty($content_data['meta_description'])) {
                $post_data['post_excerpt'] = sanitize_text_field($content_data['meta_description']);
            }
            
            // Update the post
            $result = wp_update_post($post_data, true);
            
            if (is_wp_error($result)) {
                throw new Exception('Failed to update post: ' . $result->get_error_message());
            }
            
            // Update metadata
            $this->set_post_tags($post_id, $content_data);
            $this->set_seo_metadata($post_id, $content_data, $settings);
            $this->add_custom_fields($post_id, $content_data);
            
            // Update featured image if provided
            if ($image_data) {
                $this->set_featured_image($post_id, $image_data);
            }
            
            // Update modification tracking
            update_post_meta($post_id, '_smartwriter_ai_updated_at', current_time('mysql'));
            
            $this->logger->log('success', 'Post updated successfully', [
                'post_id' => $post_id,
                'title' => $content_data['title']
            ]);
            
            return $post_id;
            
        } catch (Exception $e) {
            $this->logger->log('error', 'Failed to update post', [
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get AI generated posts
     */
    public function get_ai_generated_posts($limit = 20, $status = 'any') {
        $args = [
            'post_type' => 'post',
            'post_status' => $status,
            'posts_per_page' => $limit,
            'meta_query' => [
                [
                    'key' => '_smartwriter_ai_generated',
                    'value' => '1',
                    'compare' => '='
                ]
            ],
            'orderby' => 'date',
            'order' => 'DESC'
        ];
        
        return get_posts($args);
    }
    
    /**
     * Delete AI generated post data
     */
    public function cleanup_post_data($post_id) {
        $meta_keys = [
            '_smartwriter_ai_generated',
            '_smartwriter_ai_version',
            '_smartwriter_ai_created_at',
            '_smartwriter_ai_updated_at',
            '_smartwriter_original_data',
            '_smartwriter_content_stats',
            '_smartwriter_social_description',
            '_smartwriter_social_title'
        ];
        
        foreach ($meta_keys as $key) {
            delete_post_meta($post_id, $key);
        }
        
        $this->logger->log('info', 'AI post data cleaned up', ['post_id' => $post_id]);
    }
}