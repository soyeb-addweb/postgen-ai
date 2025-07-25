<?php
/**
 * Admin Settings Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_Admin_Settings {
    
    /**
     * Settings group
     */
    const SETTINGS_GROUP = 'smartwriter_ai_settings_group';
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_notices', [$this, 'admin_notices']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('SmartWriter AI', 'smartwriter-ai'),
            __('SmartWriter AI', 'smartwriter-ai'),
            'manage_options',
            'smartwriter-ai',
            [$this, 'render_main_page'],
            'dashicons-edit-large',
            30
        );
        
        // Settings submenu
        add_submenu_page(
            'smartwriter-ai',
            __('Settings', 'smartwriter-ai'),
            __('Settings', 'smartwriter-ai'),
            'manage_options',
            'smartwriter-ai-settings',
            [$this, 'render_settings_page']
        );
        
        // Scheduler submenu
        add_submenu_page(
            'smartwriter-ai',
            __('Schedule Posts', 'smartwriter-ai'),
            __('Schedule Posts', 'smartwriter-ai'),
            'manage_options',
            'smartwriter-ai-scheduler',
            [$this, 'render_scheduler_page']
        );
        
        // Logs submenu
        add_submenu_page(
            'smartwriter-ai',
            __('Logs', 'smartwriter-ai'),
            __('Logs', 'smartwriter-ai'),
            'manage_options',
            'smartwriter-ai-logs',
            [$this, 'render_logs_page']
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            self::SETTINGS_GROUP,
            'smartwriter_ai_settings',
            [$this, 'sanitize_settings']
        );
        
        // API Settings Section
        add_settings_section(
            'smartwriter_api_section',
            __('API Configuration', 'smartwriter-ai'),
            [$this, 'api_section_callback'],
            'smartwriter-ai-settings'
        );
        
        // Content Settings Section
        add_settings_section(
            'smartwriter_content_section',
            __('Content Configuration', 'smartwriter-ai'),
            [$this, 'content_section_callback'],
            'smartwriter-ai-settings'
        );
        
        // Schedule Settings Section
        add_settings_section(
            'smartwriter_schedule_section',
            __('Scheduling Configuration', 'smartwriter-ai'),
            [$this, 'schedule_section_callback'],
            'smartwriter-ai-settings'
        );
        
        // SEO Settings Section
        add_settings_section(
            'smartwriter_seo_section',
            __('SEO Integration', 'smartwriter-ai'),
            [$this, 'seo_section_callback'],
            'smartwriter-ai-settings'
        );
        
        // Advanced Settings Section
        add_settings_section(
            'smartwriter_advanced_section',
            __('Advanced Options', 'smartwriter-ai'),
            [$this, 'advanced_section_callback'],
            'smartwriter-ai-settings'
        );
        
        // Add fields
        $this->add_settings_fields();
    }
    
    /**
     * Add settings fields
     */
    private function add_settings_fields() {
        // API Fields
        add_settings_field(
            'api_provider',
            __('API Provider', 'smartwriter-ai'),
            [$this, 'field_api_provider'],
            'smartwriter-ai-settings',
            'smartwriter_api_section'
        );
        
        add_settings_field(
            'api_key',
            __('API Key', 'smartwriter-ai'),
            [$this, 'field_api_key'],
            'smartwriter-ai-settings',
            'smartwriter_api_section'
        );
        
        add_settings_field(
            'model',
            __('AI Model', 'smartwriter-ai'),
            [$this, 'field_model'],
            'smartwriter-ai-settings',
            'smartwriter_api_section'
        );
        
        // Content Fields
        add_settings_field(
            'prompt_template',
            __('Prompt Template', 'smartwriter-ai'),
            [$this, 'field_prompt_template'],
            'smartwriter-ai-settings',
            'smartwriter_content_section'
        );
        
        add_settings_field(
            'default_category',
            __('Default Category', 'smartwriter-ai'),
            [$this, 'field_default_category'],
            'smartwriter-ai-settings',
            'smartwriter_content_section'
        );
        
        add_settings_field(
            'auto_publish',
            __('Auto Publish', 'smartwriter-ai'),
            [$this, 'field_auto_publish'],
            'smartwriter-ai-settings',
            'smartwriter_content_section'
        );
        
        // Schedule Fields
        add_settings_field(
            'posts_per_day',
            __('Posts Per Day', 'smartwriter-ai'),
            [$this, 'field_posts_per_day'],
            'smartwriter-ai-settings',
            'smartwriter_schedule_section'
        );
        
        add_settings_field(
            'posting_interval',
            __('Posting Interval', 'smartwriter-ai'),
            [$this, 'field_posting_interval'],
            'smartwriter-ai-settings',
            'smartwriter_schedule_section'
        );
        
        add_settings_field(
            'posting_time_range',
            __('Posting Time Range', 'smartwriter-ai'),
            [$this, 'field_posting_time_range'],
            'smartwriter-ai-settings',
            'smartwriter_schedule_section'
        );
        
        // SEO Fields
        add_settings_field(
            'seo_plugin',
            __('SEO Plugin', 'smartwriter-ai'),
            [$this, 'field_seo_plugin'],
            'smartwriter-ai-settings',
            'smartwriter_seo_section'
        );
        
        // Advanced Fields
        add_settings_field(
            'generate_images',
            __('Generate Featured Images', 'smartwriter-ai'),
            [$this, 'field_generate_images'],
            'smartwriter-ai-settings',
            'smartwriter_advanced_section'
        );
        
        add_settings_field(
            'enable_backdate',
            __('Enable Backdating', 'smartwriter-ai'),
            [$this, 'field_enable_backdate'],
            'smartwriter-ai-settings',
            'smartwriter_advanced_section'
        );
    }
    
    /**
     * Section callbacks
     */
    public function api_section_callback() {
        echo '<p>' . __('Configure your AI API connection settings.', 'smartwriter-ai') . '</p>';
    }
    
    public function content_section_callback() {
        echo '<p>' . __('Configure content generation and publishing options.', 'smartwriter-ai') . '</p>';
    }
    
    public function schedule_section_callback() {
        echo '<p>' . __('Configure automatic post scheduling settings.', 'smartwriter-ai') . '</p>';
    }
    
    public function seo_section_callback() {
        echo '<p>' . __('Configure SEO plugin integration settings.', 'smartwriter-ai') . '</p>';
    }
    
    public function advanced_section_callback() {
        echo '<p>' . __('Advanced configuration options for power users.', 'smartwriter-ai') . '</p>';
    }
    
    /**
     * Field callbacks
     */
    public function field_api_provider() {
        $settings = $this->get_settings();
        $value = $settings['api_provider'] ?? 'perplexity';
        ?>
        <select name="smartwriter_ai_settings[api_provider]" id="api_provider">
            <option value="perplexity" <?php selected($value, 'perplexity'); ?>>Perplexity AI</option>
            <option value="openai" <?php selected($value, 'openai'); ?>>OpenAI</option>
            <option value="anthropic" <?php selected($value, 'anthropic'); ?>>Anthropic Claude</option>
        </select>
        <p class="description"><?php _e('Choose your preferred AI API provider.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_api_key() {
        $settings = $this->get_settings();
        $value = $settings['api_key'] ?? '';
        ?>
        <input type="password" name="smartwriter_ai_settings[api_key]" id="api_key" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <button type="button" id="test-api-connection" class="button"><?php _e('Test Connection', 'smartwriter-ai'); ?></button>
        <div id="api-test-result"></div>
        <p class="description"><?php _e('Enter your API key. Keep this secure and never share it publicly.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_model() {
        $settings = $this->get_settings();
        $value = $settings['model'] ?? 'llama-3.1-sonar-small-128k-online';
        ?>
        <select name="smartwriter_ai_settings[model]" id="model">
            <option value="llama-3.1-sonar-small-128k-online" <?php selected($value, 'llama-3.1-sonar-small-128k-online'); ?>>Llama 3.1 Sonar Small (Recommended)</option>
            <option value="llama-3.1-sonar-large-128k-online" <?php selected($value, 'llama-3.1-sonar-large-128k-online'); ?>>Llama 3.1 Sonar Large</option>
            <option value="llama-3.1-sonar-huge-128k-online" <?php selected($value, 'llama-3.1-sonar-huge-128k-online'); ?>>Llama 3.1 Sonar Huge</option>
        </select>
        <p class="description"><?php _e('Select the AI model to use for content generation.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_prompt_template() {
        $settings = $this->get_settings();
        $value = $settings['prompt_template'] ?? 'Write a comprehensive blog post about {topic}. Include an engaging title, detailed content (600-800 words), meta description, and relevant tags. Format the response as JSON with keys: title, content, meta_description, tags.';
        ?>
        <textarea name="smartwriter_ai_settings[prompt_template]" id="prompt_template" rows="6" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <button type="button" id="preview-content" class="button"><?php _e('Preview Content', 'smartwriter-ai'); ?></button>
        <div id="content-preview"></div>
        <p class="description">
            <?php _e('Use placeholders like {topic}, {date}, {keyword}. The AI will replace these with actual values.', 'smartwriter-ai'); ?>
            <br>
            <strong><?php _e('Available placeholders:', 'smartwriter-ai'); ?></strong> {topic}, {date}, {keyword}, {category}, {author}
        </p>
        <?php
    }
    
    public function field_default_category() {
        $settings = $this->get_settings();
        $value = $settings['default_category'] ?? 1;
        $categories = get_categories(['hide_empty' => false]);
        ?>
        <select name="smartwriter_ai_settings[default_category]" id="default_category">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category->term_id; ?>" <?php selected($value, $category->term_id); ?>>
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Default category for generated posts.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_auto_publish() {
        $settings = $this->get_settings();
        $value = $settings['auto_publish'] ?? true;
        ?>
        <label>
            <input type="checkbox" name="smartwriter_ai_settings[auto_publish]" value="1" <?php checked($value, 1); ?> />
            <?php _e('Automatically publish generated posts', 'smartwriter-ai'); ?>
        </label>
        <p class="description"><?php _e('If unchecked, posts will be saved as drafts for manual review.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_posts_per_day() {
        $settings = $this->get_settings();
        $value = $settings['posts_per_day'] ?? 2;
        ?>
        <input type="number" name="smartwriter_ai_settings[posts_per_day]" id="posts_per_day" value="<?php echo esc_attr($value); ?>" min="1" max="24" />
        <p class="description"><?php _e('Maximum number of posts to generate per day.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_posting_interval() {
        $settings = $this->get_settings();
        $value = $settings['posting_interval'] ?? 'smartwriter_4hours';
        ?>
        <select name="smartwriter_ai_settings[posting_interval]" id="posting_interval">
            <option value="smartwriter_30min" <?php selected($value, 'smartwriter_30min'); ?>><?php _e('Every 30 minutes', 'smartwriter-ai'); ?></option>
            <option value="hourly" <?php selected($value, 'hourly'); ?>><?php _e('Every hour', 'smartwriter-ai'); ?></option>
            <option value="smartwriter_2hours" <?php selected($value, 'smartwriter_2hours'); ?>><?php _e('Every 2 hours', 'smartwriter-ai'); ?></option>
            <option value="smartwriter_4hours" <?php selected($value, 'smartwriter_4hours'); ?>><?php _e('Every 4 hours', 'smartwriter-ai'); ?></option>
            <option value="smartwriter_6hours" <?php selected($value, 'smartwriter_6hours'); ?>><?php _e('Every 6 hours', 'smartwriter-ai'); ?></option>
            <option value="daily" <?php selected($value, 'daily'); ?>><?php _e('Daily', 'smartwriter-ai'); ?></option>
        </select>
        <p class="description"><?php _e('Interval between automatic post generation.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_posting_time_range() {
        $settings = $this->get_settings();
        $start_time = $settings['start_time'] ?? '09:00';
        $end_time = $settings['end_time'] ?? '18:00';
        ?>
        <label for="start_time"><?php _e('Start Time:', 'smartwriter-ai'); ?></label>
        <input type="time" name="smartwriter_ai_settings[start_time]" id="start_time" value="<?php echo esc_attr($start_time); ?>" />
        
        <label for="end_time" style="margin-left: 20px;"><?php _e('End Time:', 'smartwriter-ai'); ?></label>
        <input type="time" name="smartwriter_ai_settings[end_time]" id="end_time" value="<?php echo esc_attr($end_time); ?>" />
        
        <p class="description"><?php _e('Posts will only be published within this time range.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_seo_plugin() {
        $settings = $this->get_settings();
        $value = $settings['seo_plugin'] ?? 'yoast';
        ?>
        <select name="smartwriter_ai_settings[seo_plugin]" id="seo_plugin">
            <option value="yoast" <?php selected($value, 'yoast'); ?>>Yoast SEO</option>
            <option value="aioseo" <?php selected($value, 'aioseo'); ?>>All in One SEO</option>
            <option value="rankmath" <?php selected($value, 'rankmath'); ?>>RankMath</option>
            <option value="none" <?php selected($value, 'none'); ?>><?php _e('None', 'smartwriter-ai'); ?></option>
        </select>
        <p class="description"><?php _e('Choose your SEO plugin for automatic meta data integration.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_generate_images() {
        $settings = $this->get_settings();
        $value = $settings['generate_images'] ?? false;
        $image_api = $settings['image_api'] ?? 'unsplash';
        $image_api_key = $settings['image_api_key'] ?? '';
        ?>
        <label>
            <input type="checkbox" name="smartwriter_ai_settings[generate_images]" value="1" <?php checked($value, 1); ?> id="generate_images_toggle" />
            <?php _e('Automatically generate featured images', 'smartwriter-ai'); ?>
        </label>
        
        <div id="image_api_settings" style="margin-top: 10px; <?php echo $value ? '' : 'display: none;'; ?>">
            <label for="image_api"><?php _e('Image API:', 'smartwriter-ai'); ?></label>
            <select name="smartwriter_ai_settings[image_api]" id="image_api">
                <option value="unsplash" <?php selected($image_api, 'unsplash'); ?>>Unsplash</option>
                <option value="pexels" <?php selected($image_api, 'pexels'); ?>>Pexels</option>
                <option value="dall-e" <?php selected($image_api, 'dall-e'); ?>>DALL-E</option>
            </select>
            
            <br><br>
            <label for="image_api_key"><?php _e('Image API Key:', 'smartwriter-ai'); ?></label>
            <input type="password" name="smartwriter_ai_settings[image_api_key]" id="image_api_key" value="<?php echo esc_attr($image_api_key); ?>" class="regular-text" />
        </div>
        
        <p class="description"><?php _e('Generate and set featured images for posts automatically.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    public function field_enable_backdate() {
        $settings = $this->get_settings();
        $value = $settings['enable_backdate'] ?? false;
        $backdate_start = $settings['backdate_start'] ?? '';
        $backdate_end = $settings['backdate_end'] ?? '';
        ?>
        <label>
            <input type="checkbox" name="smartwriter_ai_settings[enable_backdate]" value="1" <?php checked($value, 1); ?> id="enable_backdate_toggle" />
            <?php _e('Enable backdating posts', 'smartwriter-ai'); ?>
        </label>
        
        <div id="backdate_settings" style="margin-top: 10px; <?php echo $value ? '' : 'display: none;'; ?>">
            <label for="backdate_start"><?php _e('Start Date:', 'smartwriter-ai'); ?></label>
            <input type="date" name="smartwriter_ai_settings[backdate_start]" id="backdate_start" value="<?php echo esc_attr($backdate_start); ?>" />
            
            <label for="backdate_end" style="margin-left: 20px;"><?php _e('End Date:', 'smartwriter-ai'); ?></label>
            <input type="date" name="smartwriter_ai_settings[backdate_end]" id="backdate_end" value="<?php echo esc_attr($backdate_end); ?>" />
        </div>
        
        <p class="description"><?php _e('Generate posts with past publication dates to fill content gaps.', 'smartwriter-ai'); ?></p>
        <?php
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = [];
        
        // API settings
        $sanitized['api_provider'] = sanitize_text_field($input['api_provider'] ?? 'perplexity');
        $sanitized['api_key'] = sanitize_text_field($input['api_key'] ?? '');
        $sanitized['model'] = sanitize_text_field($input['model'] ?? 'llama-3.1-sonar-small-128k-online');
        
        // Content settings
        $sanitized['prompt_template'] = sanitize_textarea_field($input['prompt_template'] ?? '');
        $sanitized['default_category'] = absint($input['default_category'] ?? 1);
        $sanitized['auto_publish'] = !empty($input['auto_publish']);
        
        // Schedule settings
        $sanitized['posts_per_day'] = absint($input['posts_per_day'] ?? 2);
        $sanitized['posting_interval'] = sanitize_text_field($input['posting_interval'] ?? 'smartwriter_4hours');
        $sanitized['start_time'] = sanitize_text_field($input['start_time'] ?? '09:00');
        $sanitized['end_time'] = sanitize_text_field($input['end_time'] ?? '18:00');
        
        // SEO settings
        $sanitized['seo_plugin'] = sanitize_text_field($input['seo_plugin'] ?? 'yoast');
        
        // Advanced settings
        $sanitized['generate_images'] = !empty($input['generate_images']);
        $sanitized['image_api'] = sanitize_text_field($input['image_api'] ?? 'unsplash');
        $sanitized['image_api_key'] = sanitize_text_field($input['image_api_key'] ?? '');
        $sanitized['enable_backdate'] = !empty($input['enable_backdate']);
        $sanitized['backdate_start'] = sanitize_text_field($input['backdate_start'] ?? '');
        $sanitized['backdate_end'] = sanitize_text_field($input['backdate_end'] ?? '');
        
        return $sanitized;
    }
    
    /**
     * Get settings
     */
    private function get_settings() {
        return get_option('smartwriter_ai_settings', []);
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        $settings = $this->get_settings();
        
        if (empty($settings['api_key'])) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <?php _e('SmartWriter AI: Please configure your API key in', 'smartwriter-ai'); ?>
                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings'); ?>">
                        <?php _e('settings', 'smartwriter-ai'); ?>
                    </a>
                    <?php _e('to start generating content.', 'smartwriter-ai'); ?>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Render main page
     */
    public function render_main_page() {
        include SMARTWRITER_AI_DIR . 'templates/admin-main.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        include SMARTWRITER_AI_DIR . 'templates/admin-settings.php';
    }
    
    /**
     * Render scheduler page
     */
    public function render_scheduler_page() {
        include SMARTWRITER_AI_DIR . 'templates/admin-scheduler.php';
    }
    
    /**
     * Render logs page
     */
    public function render_logs_page() {
        include SMARTWRITER_AI_DIR . 'templates/admin-logs.php';
    }
}