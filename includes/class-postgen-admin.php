<?php
defined('ABSPATH') || exit;

class PostGen_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('PostGen AI Settings', 'postgen-ai'),
            __('PostGen AI', 'postgen-ai'),
            'manage_options',
            'postgen-ai',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('postgen_ai_settings_group', 'postgen_ai_api_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('postgen_ai_settings_group', 'postgen_ai_prompt_template', ['sanitize_callback' => 'sanitize_textarea_field']);
    }

    public function render_settings_page() {
        include POSTGEN_AI_DIR . 'templates/settings-page.php';
    }
}
