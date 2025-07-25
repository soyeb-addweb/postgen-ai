<?php

/**
 * Plugin Name: PostGen AI
 * Plugin URI: https://example.com/
 * Description: Automatically generate and schedule posts using Perplexity AI or similar API.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: postgen-ai
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('POSTGEN_AI_VERSION', '1.0.0');
define('POSTGEN_AI_DIR', plugin_dir_path(__FILE__));
define('POSTGEN_AI_URL', plugin_dir_url(__FILE__));

// Include core files
require_once POSTGEN_AI_DIR . 'includes/class-postgen-admin.php';
require_once POSTGEN_AI_DIR . 'includes/class-postgen-api.php';
require_once POSTGEN_AI_DIR . 'includes/class-postgen-scheduler.php';
require_once POSTGEN_AI_DIR . 'includes/class-postgen-postbuilder.php';
require_once POSTGEN_AI_DIR . 'includes/class-postgen-logger.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    if (is_admin()) {
        new PostGen_Admin();
    }
});
