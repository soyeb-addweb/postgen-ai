<?php

/**
 * Plugin Name: SmartWriter AI - AI Blog Post Generator
 * Plugin URI: https://smartwriter-ai.com/
 * Description: Automatically generate and schedule WordPress posts using Perplexity AI. Features include post scheduling, SEO optimization, content mapping, and intelligent auto-posting with CRON.
 * Version: 1.0.0
 * Author: SmartWriter AI Team
 * License: GPL v2 or later
 * Text Domain: smartwriter-ai
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Plugin constants
define('SMARTWRITER_AI_VERSION', '1.0.0');
define('SMARTWRITER_AI_DIR', plugin_dir_path(__FILE__));
define('SMARTWRITER_AI_URL', plugin_dir_url(__FILE__));
define('SMARTWRITER_AI_BASENAME', plugin_basename(__FILE__));

// Include core files
require_once SMARTWRITER_AI_DIR . 'includes/class-smartwriter-ai.php';
require_once SMARTWRITER_AI_DIR . 'includes/class-admin-settings.php';
require_once SMARTWRITER_AI_DIR . 'includes/class-api-connector.php';
require_once SMARTWRITER_AI_DIR . 'includes/class-scheduler.php';
require_once SMARTWRITER_AI_DIR . 'includes/class-post-creator.php';
require_once SMARTWRITER_AI_DIR . 'includes/class-logger.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    SmartWriter_AI::get_instance();
});

// Activation hook
register_activation_hook(__FILE__, function() {
    SmartWriter_AI::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    SmartWriter_AI::deactivate();
});

// Uninstall hook
register_uninstall_hook(__FILE__, function() {
    SmartWriter_AI::uninstall();
});
