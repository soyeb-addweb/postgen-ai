<?php
/**
 * Scheduler Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_Scheduler {
    
    /**
     * Logger instance
     */
    private $logger;
    
    /**
     * API connector instance
     */
    private $api_connector;
    
    /**
     * Post creator instance
     */
    private $post_creator;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->logger = new SmartWriter_Logger();
        $this->api_connector = new SmartWriter_API_Connector();
        $this->post_creator = new SmartWriter_Post_Creator();
        
        add_action('init', [$this, 'init_scheduler']);
    }
    
    /**
     * Initialize scheduler
     */
    public function init_scheduler() {
        $this->setup_cron_schedules();
        $this->maybe_schedule_posts();
    }
    
    /**
     * Setup CRON schedules
     */
    private function setup_cron_schedules() {
        $settings = get_option('smartwriter_ai_settings', []);
        
        if (empty($settings['api_key']) || !$settings['auto_publish']) {
            return;
        }
        
        $interval = $settings['posting_interval'] ?? 'smartwriter_4hours';
        
        // Clear existing schedule
        wp_clear_scheduled_hook('smartwriter_ai_generate_post');
        
        // Schedule new event
        if (!wp_next_scheduled('smartwriter_ai_generate_post')) {
            wp_schedule_event(time(), $interval, 'smartwriter_ai_generate_post');
            
            $this->logger->log('info', 'Post generation scheduled', [
                'interval' => $interval,
                'next_run' => wp_next_scheduled('smartwriter_ai_generate_post')
            ]);
        }
    }
    
    /**
     * Maybe schedule posts for backdating
     */
    private function maybe_schedule_posts() {
        $settings = get_option('smartwriter_ai_settings', []);
        
        if (!$settings['enable_backdate'] || empty($settings['backdate_start']) || empty($settings['backdate_end'])) {
            return;
        }
        
        // Check if backdate posts have been scheduled
        if (get_option('smartwriter_ai_backdate_scheduled', false)) {
            return;
        }
        
        $this->schedule_backdate_posts($settings['backdate_start'], $settings['backdate_end']);
        update_option('smartwriter_ai_backdate_scheduled', true);
    }
    
    /**
     * Schedule backdate posts
     */
    public function schedule_backdate_posts($start_date, $end_date) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $settings = get_option('smartwriter_ai_settings', []);
        
        $posts_per_day = $settings['posts_per_day'] ?? 2;
        $start_time = $settings['start_time'] ?? '09:00';
        $end_time = $settings['end_time'] ?? '18:00';
        
        $current_date = clone $start;
        $topics = $this->generate_backdate_topics();
        $topic_index = 0;
        
        while ($current_date <= $end) {
            // Skip weekends if needed (optional feature)
            if ($current_date->format('N') >= 6) {
                $current_date->modify('+1 day');
                continue;
            }
            
            // Generate posting times for this day
            $posting_times = $this->generate_posting_times($posts_per_day, $start_time, $end_time);
            
            foreach ($posting_times as $time) {
                $schedule_datetime = clone $current_date;
                $schedule_datetime->setTime(...explode(':', $time));
                
                // Only schedule if date is in the past
                if ($schedule_datetime < new DateTime()) {
                    $topic = $topics[$topic_index % count($topics)];
                    $this->add_scheduled_post($topic, $schedule_datetime->format('Y-m-d H:i:s'));
                    $topic_index++;
                }
            }
            
            $current_date->modify('+1 day');
        }
        
        $this->logger->log('info', 'Backdate posts scheduled', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_posts' => $topic_index
        ]);
    }
    
    /**
     * Generate posting times for a day
     */
    private function generate_posting_times($posts_per_day, $start_time, $end_time) {
        $start_hour = (int) explode(':', $start_time)[0];
        $start_minute = (int) explode(':', $start_time)[1];
        $end_hour = (int) explode(':', $end_time)[0];
        $end_minute = (int) explode(':', $end_time)[1];
        
        $total_minutes = ($end_hour * 60 + $end_minute) - ($start_hour * 60 + $start_minute);
        $interval_minutes = $total_minutes / $posts_per_day;
        
        $times = [];
        for ($i = 0; $i < $posts_per_day; $i++) {
            $minutes_from_start = $i * $interval_minutes + rand(0, $interval_minutes * 0.2); // Add some randomness
            $hour = $start_hour + floor(($start_minute + $minutes_from_start) / 60);
            $minute = ($start_minute + $minutes_from_start) % 60;
            
            $times[] = sprintf('%02d:%02d', $hour, $minute);
        }
        
        return $times;
    }
    
    /**
     * Generate topics for backdate posts
     */
    private function generate_backdate_topics() {
        return [
            'The Evolution of Artificial Intelligence in Business',
            'Digital Marketing Trends That Changed Everything',
            'Remote Work: Transforming the Modern Workplace',
            'Cybersecurity Essentials for Small Businesses',
            'The Rise of E-commerce and Online Shopping',
            'Social Media Marketing: Best Practices and Strategies',
            'Cloud Computing: Revolutionizing Data Storage',
            'Mobile App Development: Trends and Technologies',
            'Search Engine Optimization: Advanced Techniques',
            'Content Marketing: Creating Engaging Digital Experiences',
            'Data Analytics: Driving Business Intelligence',
            'Automation Tools: Boosting Productivity and Efficiency',
            'Customer Experience: Building Brand Loyalty',
            'Sustainable Business Practices: Going Green',
            'Financial Technology: The Future of Banking',
            'Virtual Reality: Applications Beyond Gaming',
            'Machine Learning: Practical Business Applications',
            'Blockchain Technology: Beyond Cryptocurrency',
            'Internet of Things: Connecting Our World',
            'Startup Success Stories: Lessons Learned'
        ];
    }
    
    /**
     * Add scheduled post to database
     */
    public function add_scheduled_post($prompt, $schedule_date) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        
        $result = $wpdb->insert(
            $table_name,
            [
                'prompt' => $prompt,
                'schedule_date' => $schedule_date,
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s']
        );
        
        if ($result === false) {
            $this->logger->log('error', 'Failed to add scheduled post', [
                'prompt' => $prompt,
                'schedule_date' => $schedule_date,
                'error' => $wpdb->last_error
            ]);
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Process scheduled post (CRON callback)
     */
    public function process_scheduled_post() {
        if (!$this->can_post_now()) {
            return;
        }
        
        $settings = get_option('smartwriter_ai_settings', []);
        $prompt_template = $settings['prompt_template'] ?? 'Write a comprehensive blog post about {topic}.';
        $topic = $this->get_next_topic();
        
        $this->generate_and_publish_post($prompt_template, $topic);
    }
    
    /**
     * Process batch of scheduled posts
     */
    public function process_batch() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        $current_time = current_time('mysql');
        
        // Get pending posts that should be processed
        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE status = 'pending' 
             AND schedule_date <= %s 
             ORDER BY schedule_date ASC 
             LIMIT 5",
            $current_time
        ));
        
        if (empty($posts)) {
            return;
        }
        
        foreach ($posts as $scheduled_post) {
            $this->process_single_scheduled_post($scheduled_post);
            
            // Add delay between posts
            sleep(2);
        }
    }
    
    /**
     * Process single scheduled post
     */
    private function process_single_scheduled_post($scheduled_post) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        
        // Mark as processing
        $wpdb->update(
            $table_name,
            ['status' => 'processing'],
            ['id' => $scheduled_post->id],
            ['%s'],
            ['%d']
        );
        
        try {
            $post_id = $this->generate_and_publish_post(
                $scheduled_post->prompt,
                null,
                $scheduled_post->schedule_date
            );
            
            if ($post_id) {
                $wpdb->update(
                    $table_name,
                    [
                        'status' => 'completed',
                        'post_id' => $post_id,
                        'processed_at' => current_time('mysql')
                    ],
                    ['id' => $scheduled_post->id],
                    ['%s', '%d', '%s'],
                    ['%d']
                );
                
                $this->logger->log('success', 'Scheduled post processed successfully', [
                    'scheduled_post_id' => $scheduled_post->id,
                    'post_id' => $post_id,
                    'schedule_date' => $scheduled_post->schedule_date
                ]);
            } else {
                throw new Exception('Failed to create post');
            }
        } catch (Exception $e) {
            $wpdb->update(
                $table_name,
                [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'processed_at' => current_time('mysql')
                ],
                ['id' => $scheduled_post->id],
                ['%s', '%s', '%s'],
                ['%d']
            );
            
            $this->logger->log('error', 'Scheduled post processing failed', [
                'scheduled_post_id' => $scheduled_post->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate and publish post
     */
    private function generate_and_publish_post($prompt, $topic = null, $schedule_date = null) {
        $content = $this->api_connector->generate_content($prompt, $topic);
        
        if (!$content) {
            throw new Exception('Failed to generate content');
        }
        
        // Get featured image if enabled
        $image_data = null;
        $settings = get_option('smartwriter_ai_settings', []);
        if ($settings['generate_images']) {
            $image_data = $this->api_connector->get_featured_image(
                $topic ?: $content['focus_keyword'],
                $content['title']
            );
        }
        
        $post_id = $this->post_creator->create_post($content, $image_data, $schedule_date);
        
        if (!$post_id) {
            throw new Exception('Failed to create WordPress post');
        }
        
        return $post_id;
    }
    
    /**
     * Check if we can post now
     */
    private function can_post_now() {
        $settings = get_option('smartwriter_ai_settings', []);
        
        // Check if within posting time window
        $current_time = current_time('H:i');
        $start_time = $settings['start_time'] ?? '09:00';
        $end_time = $settings['end_time'] ?? '18:00';
        
        if ($current_time < $start_time || $current_time > $end_time) {
            return false;
        }
        
        // Check daily post limit
        $posts_today = $this->count_posts_today();
        $max_posts = $settings['posts_per_day'] ?? 2;
        
        if ($posts_today >= $max_posts) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Count posts created today
     */
    private function count_posts_today() {
        $today = current_time('Y-m-d');
        
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => ['publish', 'draft'],
            'meta_query' => [
                [
                    'key' => '_smartwriter_ai_generated',
                    'value' => '1',
                    'compare' => '='
                ]
            ],
            'date_query' => [
                [
                    'year' => date('Y', strtotime($today)),
                    'month' => date('m', strtotime($today)),
                    'day' => date('d', strtotime($today))
                ]
            ],
            'fields' => 'ids'
        ]);
        
        return count($posts);
    }
    
    /**
     * Get next topic
     */
    private function get_next_topic() {
        $topics = [
            'Latest Technology Trends',
            'Digital Marketing Strategies',
            'Business Growth Tips',
            'Productivity and Automation',
            'Industry Innovation',
            'Customer Success Stories',
            'Market Analysis and Insights',
            'Professional Development',
            'Software and Tools Review',
            'Future Predictions and Trends'
        ];
        
        return $topics[array_rand($topics)];
    }
    
    /**
     * Get scheduled posts
     */
    public function get_scheduled_posts($limit = 50, $status = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        $where_clause = '';
        $params = [];
        
        if ($status) {
            $where_clause = ' WHERE status = %s';
            $params[] = $status;
        }
        
        $query = "SELECT * FROM $table_name $where_clause ORDER BY schedule_date DESC LIMIT %d";
        $params[] = $limit;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Delete scheduled post
     */
    public function delete_scheduled_post($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        
        $result = $wpdb->delete($table_name, ['id' => $id], ['%d']);
        
        if ($result !== false) {
            $this->logger->log('info', 'Scheduled post deleted', ['id' => $id]);
        }
        
        return $result !== false;
    }
    
    /**
     * Update scheduled post
     */
    public function update_scheduled_post($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_scheduled_posts';
        
        $allowed_fields = ['prompt', 'schedule_date', 'status'];
        $update_data = [];
        $format = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $update_data[$key] = $value;
                $format[] = '%s';
            }
        }
        
        if (empty($update_data)) {
            return false;
        }
        
        $result = $wpdb->update(
            $table_name,
            $update_data,
            ['id' => $id],
            $format,
            ['%d']
        );
        
        if ($result !== false) {
            $this->logger->log('info', 'Scheduled post updated', [
                'id' => $id,
                'data' => $update_data
            ]);
        }
        
        return $result !== false;
    }
    
    /**
     * Get scheduler status
     */
    public function get_scheduler_status() {
        $next_scheduled = wp_next_scheduled('smartwriter_ai_generate_post');
        $settings = get_option('smartwriter_ai_settings', []);
        
        return [
            'active' => !empty($settings['api_key']) && $settings['auto_publish'],
            'next_run' => $next_scheduled ? date('Y-m-d H:i:s', $next_scheduled) : null,
            'interval' => $settings['posting_interval'] ?? 'smartwriter_4hours',
            'posts_today' => $this->count_posts_today(),
            'max_posts_per_day' => $settings['posts_per_day'] ?? 2,
            'posting_window' => [
                'start' => $settings['start_time'] ?? '09:00',
                'end' => $settings['end_time'] ?? '18:00'
            ]
        ];
    }
    
    /**
     * Force run scheduler (for testing)
     */
    public function force_run() {
        $this->process_scheduled_post();
        $this->process_batch();
    }
}