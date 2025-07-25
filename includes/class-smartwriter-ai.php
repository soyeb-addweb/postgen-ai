<?php
/**
 * Main SmartWriter AI Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_AI {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Plugin components
     */
    private $admin_settings;
    private $api_connector;
    private $scheduler;
    private $post_creator;
    private $logger;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'load_textdomain']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_enqueue_scripts']);
        
        // AJAX hooks
        add_action('wp_ajax_smartwriter_test_api', [$this, 'ajax_test_api']);
        add_action('wp_ajax_smartwriter_preview_content', [$this, 'ajax_preview_content']);
        add_action('wp_ajax_smartwriter_get_logs', [$this, 'ajax_get_logs']);
        
        // Custom CRON intervals
        add_filter('cron_schedules', [$this, 'custom_cron_intervals']);
        
        // Custom CRON hooks
        add_action('smartwriter_ai_generate_post', [$this, 'cron_generate_post']);
        add_action('smartwriter_ai_batch_process', [$this, 'cron_batch_process']);
    }
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->logger = new SmartWriter_Logger();
        $this->api_connector = new SmartWriter_API_Connector();
        $this->post_creator = new SmartWriter_Post_Creator();
        $this->scheduler = new SmartWriter_Scheduler();
        
        if (is_admin()) {
            $this->admin_settings = new SmartWriter_Admin_Settings();
        }
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'smartwriter-ai',
            false,
            dirname(SMARTWRITER_AI_BASENAME) . '/languages'
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'smartwriter-ai') === false) {
            return;
        }
        
        wp_enqueue_script(
            'smartwriter-ai-admin',
            SMARTWRITER_AI_URL . 'assets/admin.js',
            ['jquery', 'wp-util'],
            SMARTWRITER_AI_VERSION,
            true
        );
        
        wp_enqueue_style(
            'smartwriter-ai-admin',
            SMARTWRITER_AI_URL . 'assets/admin.css',
            [],
            SMARTWRITER_AI_VERSION
        );
        
        wp_localize_script('smartwriter-ai-admin', 'smartwriterAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('smartwriter_ai_nonce'),
            'strings' => [
                'testing' => __('Testing API connection...', 'smartwriter-ai'),
                'success' => __('API connection successful!', 'smartwriter-ai'),
                'error' => __('API connection failed. Please check your settings.', 'smartwriter-ai'),
                'generating' => __('Generating preview...', 'smartwriter-ai'),
                'confirm_schedule' => __('Are you sure you want to schedule these posts?', 'smartwriter-ai'),
            ]
        ]);
    }
    
    /**
     * Enqueue frontend scripts (if needed)
     */
    public function frontend_enqueue_scripts() {
        // Frontend scripts if needed in future
    }
    
    /**
     * AJAX: Test API connection
     */
    public function ajax_test_api() {
        check_ajax_referer('smartwriter_ai_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'smartwriter-ai'));
        }
        
        $api_key = sanitize_text_field($_POST['api_key'] ?? '');
        
        if (empty($api_key)) {
            wp_send_json_error(__('API key is required', 'smartwriter-ai'));
        }
        
        $test_result = $this->api_connector->test_connection($api_key);
        
        if ($test_result) {
            wp_send_json_success([
                'message' => __('API connection successful!', 'smartwriter-ai'),
                'models' => $test_result['models'] ?? []
            ]);
        } else {
            wp_send_json_error(__('API connection failed', 'smartwriter-ai'));
        }
    }
    
    /**
     * AJAX: Preview content generation
     */
    public function ajax_preview_content() {
        check_ajax_referer('smartwriter_ai_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'smartwriter-ai'));
        }
        
        $prompt = sanitize_textarea_field($_POST['prompt'] ?? '');
        
        if (empty($prompt)) {
            wp_send_json_error(__('Prompt is required', 'smartwriter-ai'));
        }
        
        $content = $this->api_connector->generate_content($prompt);
        
        if ($content) {
            wp_send_json_success($content);
        } else {
            wp_send_json_error(__('Failed to generate content', 'smartwriter-ai'));
        }
    }
    
    /**
     * AJAX: Get logs
     */
    public function ajax_get_logs() {
        check_ajax_referer('smartwriter_ai_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'smartwriter-ai'));
        }
        
        $logs = $this->logger->get_logs();
        wp_send_json_success($logs);
    }
    
    /**
     * Add custom CRON intervals
     */
    public function custom_cron_intervals($schedules) {
        $schedules['smartwriter_30min'] = [
            'interval' => 30 * 60,
            'display' => __('Every 30 Minutes', 'smartwriter-ai')
        ];
        
        $schedules['smartwriter_2hours'] = [
            'interval' => 2 * 60 * 60,
            'display' => __('Every 2 Hours', 'smartwriter-ai')
        ];
        
        $schedules['smartwriter_4hours'] = [
            'interval' => 4 * 60 * 60,
            'display' => __('Every 4 Hours', 'smartwriter-ai')
        ];
        
        $schedules['smartwriter_6hours'] = [
            'interval' => 6 * 60 * 60,
            'display' => __('Every 6 Hours', 'smartwriter-ai')
        ];
        
        return $schedules;
    }
    
    /**
     * CRON: Generate single post
     */
    public function cron_generate_post() {
        $this->scheduler->process_scheduled_post();
    }
    
    /**
     * CRON: Batch process posts
     */
    public function cron_batch_process() {
        $this->scheduler->process_batch();
    }
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Schedule initial CRON event
        if (!wp_next_scheduled('smartwriter_ai_batch_process')) {
            wp_schedule_event(time(), 'hourly', 'smartwriter_ai_batch_process');
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('smartwriter_ai_generate_post');
        wp_clear_scheduled_hook('smartwriter_ai_batch_process');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin uninstall
     */
    public static function uninstall() {
        // Remove options
        delete_option('smartwriter_ai_settings');
        delete_option('smartwriter_ai_api_key');
        delete_option('smartwriter_ai_version');
        
        // Drop custom tables
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}smartwriter_logs");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}smartwriter_scheduled_posts");
        
        // Clear any remaining scheduled events
        wp_clear_scheduled_hook('smartwriter_ai_generate_post');
        wp_clear_scheduled_hook('smartwriter_ai_batch_process');
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Logs table
        $logs_table = $wpdb->prefix . 'smartwriter_logs';
        $logs_sql = "CREATE TABLE $logs_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            message text NOT NULL,
            data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Scheduled posts table
        $scheduled_table = $wpdb->prefix . 'smartwriter_scheduled_posts';
        $scheduled_sql = "CREATE TABLE $scheduled_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            prompt text NOT NULL,
            schedule_date datetime NOT NULL,
            status varchar(20) DEFAULT 'pending',
            post_id int(11) NULL,
            error_message text NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            processed_at datetime NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY schedule_date (schedule_date)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($logs_sql);
        dbDelta($scheduled_sql);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_settings = [
            'api_provider' => 'perplexity',
            'api_key' => '',
            'model' => 'llama-3.1-sonar-small-128k-online',
            'prompt_template' => 'Write a comprehensive blog post about {topic}. Include an engaging title, detailed content (600-800 words), meta description, and relevant tags. Format the response as JSON with keys: title, content, meta_description, tags.',
            'posts_per_day' => 2,
            'posting_interval' => 'smartwriter_4hours',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'default_category' => 1,
            'auto_publish' => true,
            'seo_plugin' => 'yoast',
            'generate_images' => false,
            'image_api' => 'unsplash',
            'image_api_key' => '',
            'enable_backdate' => false,
            'backdate_start' => '',
            'backdate_end' => ''
        ];
        
        add_option('smartwriter_ai_settings', $default_settings);
        add_option('smartwriter_ai_version', SMARTWRITER_AI_VERSION);
    }
}