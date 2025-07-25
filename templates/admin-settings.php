<?php
/**
 * Admin Settings Template
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Handle form submission
if (isset($_POST['submit']) && check_admin_referer('smartwriter_ai_settings_nonce')) {
    // Settings are handled by the admin class sanitization
}

$settings = get_option('smartwriter_ai_settings', []);
?>

<div class="wrap smartwriter-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e('SmartWriter AI Settings', 'smartwriter-ai'); ?>
    </h1>

    <nav class="nav-tab-wrapper smartwriter-nav-tabs">
        <a href="#api-settings" class="nav-tab nav-tab-active"><?php _e('API Configuration', 'smartwriter-ai'); ?></a>
        <a href="#content-settings" class="nav-tab"><?php _e('Content Settings', 'smartwriter-ai'); ?></a>
        <a href="#schedule-settings" class="nav-tab"><?php _e('Scheduling', 'smartwriter-ai'); ?></a>
        <a href="#seo-settings" class="nav-tab"><?php _e('SEO Integration', 'smartwriter-ai'); ?></a>
        <a href="#advanced-settings" class="nav-tab"><?php _e('Advanced', 'smartwriter-ai'); ?></a>
    </nav>

    <form method="post" action="options.php" id="smartwriter-settings-form">
        <?php
        settings_fields(SmartWriter_Admin_Settings::SETTINGS_GROUP);
        wp_nonce_field('smartwriter_ai_settings_nonce');
        ?>

        <!-- API Configuration Tab -->
        <div id="api-settings" class="smartwriter-tab-content active">
            <div class="smartwriter-settings-section">
                <h2><?php _e('API Configuration', 'smartwriter-ai'); ?></h2>
                <p class="description"><?php _e('Configure your AI API connection settings to start generating content.', 'smartwriter-ai'); ?></p>

                <table class="form-table" role="presentation">
                    <?php do_settings_fields('smartwriter-ai-settings', 'smartwriter_api_section'); ?>
                </table>

                <div class="smartwriter-info-box">
                    <h3><?php _e('Getting Your API Key', 'smartwriter-ai'); ?></h3>
                    <div class="api-instructions">
                        <div class="api-provider-instructions" data-provider="perplexity">
                            <h4>Perplexity AI</h4>
                            <ol>
                                <li><?php _e('Visit', 'smartwriter-ai'); ?> <a href="https://www.perplexity.ai/settings/api" target="_blank">Perplexity AI API Settings</a></li>
                                <li><?php _e('Create an account or sign in', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Generate a new API key', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Copy and paste the key above', 'smartwriter-ai'); ?></li>
                            </ol>
                        </div>
                        
                        <div class="api-provider-instructions" data-provider="openai" style="display: none;">
                            <h4>OpenAI</h4>
                            <ol>
                                <li><?php _e('Visit', 'smartwriter-ai'); ?> <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys</a></li>
                                <li><?php _e('Create an account or sign in', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Click "Create new secret key"', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Copy and paste the key above', 'smartwriter-ai'); ?></li>
                            </ol>
                        </div>
                        
                        <div class="api-provider-instructions" data-provider="anthropic" style="display: none;">
                            <h4>Anthropic Claude</h4>
                            <ol>
                                <li><?php _e('Visit', 'smartwriter-ai'); ?> <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a></li>
                                <li><?php _e('Create an account or sign in', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Go to API Keys section', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Generate a new API key', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Copy and paste the key above', 'smartwriter-ai'); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Settings Tab -->
        <div id="content-settings" class="smartwriter-tab-content">
            <div class="smartwriter-settings-section">
                <h2><?php _e('Content Generation Settings', 'smartwriter-ai'); ?></h2>
                <p class="description"><?php _e('Configure how your content is generated and organized.', 'smartwriter-ai'); ?></p>

                <table class="form-table" role="presentation">
                    <?php do_settings_fields('smartwriter-ai-settings', 'smartwriter_content_section'); ?>
                </table>

                <div class="smartwriter-info-box">
                    <h3><?php _e('Prompt Template Tips', 'smartwriter-ai'); ?></h3>
                    <ul>
                        <li><?php _e('Use {topic} for dynamic topic insertion', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Use {date} for current date', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Use {keyword} for SEO keyword targeting', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Request JSON format for structured responses', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Be specific about word count and style', 'smartwriter-ai'); ?></li>
                    </ul>
                    
                    <h4><?php _e('Example Prompts:', 'smartwriter-ai'); ?></h4>
                    <div class="example-prompts">
                        <div class="example-prompt">
                            <strong><?php _e('Blog Post:', 'smartwriter-ai'); ?></strong>
                            <code>Write a comprehensive 800-word blog post about {topic}. Include an engaging title, detailed content with subheadings, meta description (max 155 chars), and 5 relevant tags. Format as JSON with keys: title, content, meta_description, tags.</code>
                        </div>
                        
                        <div class="example-prompt">
                            <strong><?php _e('News Article:', 'smartwriter-ai'); ?></strong>
                            <code>Write a news-style article about {topic} with current date {date}. Include headline, lead paragraph, body content (600 words), and focus keyword. Use journalistic style. Format as JSON.</code>
                        </div>
                        
                        <div class="example-prompt">
                            <strong><?php _e('Tutorial:', 'smartwriter-ai'); ?></strong>
                            <code>Create a step-by-step tutorial about {topic}. Include introduction, numbered steps, tips, conclusion, and SEO meta description. Target keyword: {keyword}. Format as JSON.</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduling Settings Tab -->
        <div id="schedule-settings" class="smartwriter-tab-content">
            <div class="smartwriter-settings-section">
                <h2><?php _e('Post Scheduling Settings', 'smartwriter-ai'); ?></h2>
                <p class="description"><?php _e('Configure when and how often posts are automatically generated and published.', 'smartwriter-ai'); ?></p>

                <table class="form-table" role="presentation">
                    <?php do_settings_fields('smartwriter-ai-settings', 'smartwriter_schedule_section'); ?>
                </table>

                <div class="smartwriter-info-box">
                    <h3><?php _e('Scheduling Best Practices', 'smartwriter-ai'); ?></h3>
                    <ul>
                        <li><?php _e('Start with 1-2 posts per day to maintain quality', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Schedule posts during your target audience\'s active hours', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Use longer intervals (4-6 hours) for better content quality', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Monitor your API usage limits', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Review generated content regularly', 'smartwriter-ai'); ?></li>
                    </ul>
                </div>

                <div class="current-schedule-status">
                    <h3><?php _e('Current Schedule Status', 'smartwriter-ai'); ?></h3>
                    <?php
                    $next_scheduled = wp_next_scheduled('smartwriter_ai_generate_post');
                    if ($next_scheduled):
                    ?>
                        <p class="schedule-active">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php printf(__('Next post scheduled for: %s', 'smartwriter-ai'), date('M j, Y g:i A', $next_scheduled)); ?>
                        </p>
                    <?php else: ?>
                        <p class="schedule-inactive">
                            <span class="dashicons dashicons-warning"></span>
                            <?php _e('No posts currently scheduled. Save settings to activate scheduling.', 'smartwriter-ai'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SEO Integration Tab -->
        <div id="seo-settings" class="smartwriter-tab-content">
            <div class="smartwriter-settings-section">
                <h2><?php _e('SEO Plugin Integration', 'smartwriter-ai'); ?></h2>
                <p class="description"><?php _e('Configure integration with your SEO plugin for automatic meta data optimization.', 'smartwriter-ai'); ?></p>

                <table class="form-table" role="presentation">
                    <?php do_settings_fields('smartwriter-ai-settings', 'smartwriter_seo_section'); ?>
                </table>

                <div class="seo-plugins-status">
                    <h3><?php _e('Detected SEO Plugins', 'smartwriter-ai'); ?></h3>
                    <div class="plugin-status-grid">
                        <div class="plugin-status">
                            <strong>Yoast SEO:</strong>
                            <?php if (is_plugin_active('wordpress-seo/wp-seo.php')): ?>
                                <span class="status-active"><?php _e('Active', 'smartwriter-ai'); ?></span>
                            <?php else: ?>
                                <span class="status-inactive"><?php _e('Not Active', 'smartwriter-ai'); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="plugin-status">
                            <strong>All in One SEO:</strong>
                            <?php if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')): ?>
                                <span class="status-active"><?php _e('Active', 'smartwriter-ai'); ?></span>
                            <?php else: ?>
                                <span class="status-inactive"><?php _e('Not Active', 'smartwriter-ai'); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="plugin-status">
                            <strong>RankMath:</strong>
                            <?php if (is_plugin_active('seo-by-rank-math/rank-math.php')): ?>
                                <span class="status-active"><?php _e('Active', 'smartwriter-ai'); ?></span>
                            <?php else: ?>
                                <span class="status-inactive"><?php _e('Not Active', 'smartwriter-ai'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="smartwriter-info-box">
                    <h3><?php _e('SEO Integration Features', 'smartwriter-ai'); ?></h3>
                    <ul>
                        <li><?php _e('Automatic meta titles and descriptions', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Focus keyword assignment', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Content score optimization', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Social media meta tags', 'smartwriter-ai'); ?></li>
                        <li><?php _e('Structured data support', 'smartwriter-ai'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="advanced-settings" class="smartwriter-tab-content">
            <div class="smartwriter-settings-section">
                <h2><?php _e('Advanced Settings', 'smartwriter-ai'); ?></h2>
                <p class="description"><?php _e('Advanced features for power users and specific use cases.', 'smartwriter-ai'); ?></p>

                <table class="form-table" role="presentation">
                    <?php do_settings_fields('smartwriter-ai-settings', 'smartwriter_advanced_section'); ?>
                </table>

                <div class="smartwriter-info-box">
                    <h3><?php _e('Image Generation Options', 'smartwriter-ai'); ?></h3>
                    <div class="image-api-comparison">
                        <div class="api-option">
                            <h4>Unsplash</h4>
                            <ul>
                                <li><?php _e('High-quality stock photos', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Free with attribution', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Large selection', 'smartwriter-ai'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="api-option">
                            <h4>Pexels</h4>
                            <ul>
                                <li><?php _e('Professional stock photos', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Free to use', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Good variety', 'smartwriter-ai'); ?></li>
                            </ul>
                        </div>
                        
                        <div class="api-option">
                            <h4>DALL-E</h4>
                            <ul>
                                <li><?php _e('AI-generated custom images', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Unique content', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Requires OpenAI API key', 'smartwriter-ai'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="smartwriter-danger-zone">
                    <h3><?php _e('Danger Zone', 'smartwriter-ai'); ?></h3>
                    <div class="danger-actions">
                        <button type="button" id="clear-all-logs" class="button button-secondary">
                            <?php _e('Clear All Logs', 'smartwriter-ai'); ?>
                        </button>
                        
                        <button type="button" id="reset-settings" class="button button-secondary">
                            <?php _e('Reset All Settings', 'smartwriter-ai'); ?>
                        </button>
                        
                        <button type="button" id="export-settings" class="button button-secondary">
                            <?php _e('Export Settings', 'smartwriter-ai'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="smartwriter-settings-footer">
            <?php submit_button(__('Save Settings', 'smartwriter-ai'), 'primary', 'submit', false); ?>
            
            <button type="button" id="preview-settings" class="button button-secondary">
                <?php _e('Preview Content', 'smartwriter-ai'); ?>
            </button>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div id="settings-preview-modal" class="smartwriter-modal" style="display: none;">
    <div class="modal-content large">
        <div class="modal-header">
            <h3><?php _e('Content Preview', 'smartwriter-ai'); ?></h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="preview-loading" style="display: none;">
                <p><?php _e('Generating preview content...', 'smartwriter-ai'); ?></p>
                <div class="preview-spinner"></div>
            </div>
            <div id="preview-content"></div>
        </div>
    </div>
</div>

<style>
.smartwriter-admin {
    max-width: 1200px;
}

.smartwriter-nav-tabs {
    margin-bottom: 20px;
}

.smartwriter-tab-content {
    display: none;
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-top: none;
}

.smartwriter-tab-content.active {
    display: block;
}

.smartwriter-settings-section {
    margin-bottom: 30px;
}

.smartwriter-info-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 20px;
    margin-top: 20px;
}

.smartwriter-info-box h3 {
    margin-top: 0;
    color: #2c3e50;
}

.example-prompts .example-prompt {
    margin-bottom: 15px;
    padding: 10px;
    background: #fff;
    border-radius: 3px;
    border-left: 4px solid #007cba;
}

.example-prompt code {
    display: block;
    margin-top: 5px;
    padding: 8px;
    background: #f1f1f1;
    border-radius: 3px;
    font-size: 12px;
    line-height: 1.4;
}

.current-schedule-status {
    background: #f0f8ff;
    border: 1px solid #b8deff;
    border-radius: 4px;
    padding: 15px;
    margin-top: 20px;
}

.schedule-active {
    color: #006600;
}

.schedule-inactive {
    color: #cc6600;
}

.seo-plugins-status {
    margin-top: 20px;
}

.plugin-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.plugin-status {
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}

.status-active {
    color: #28a745;
    font-weight: bold;
}

.status-inactive {
    color: #6c757d;
}

.image-api-comparison {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.api-option {
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.api-option h4 {
    margin-top: 0;
    color: #2c3e50;
}

.smartwriter-danger-zone {
    background: #fff5f5;
    border: 1px solid #feb2b2;
    border-radius: 4px;
    padding: 20px;
    margin-top: 30px;
}

.smartwriter-danger-zone h3 {
    color: #c53030;
    margin-top: 0;
}

.danger-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.smartwriter-settings-footer {
    background: #f8f9fa;
    padding: 20px;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
    align-items: center;
}

.smartwriter-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #fff;
    border-radius: 4px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-content.large {
    max-width: 800px;
}

.modal-header {
    padding: 20px 20px 15px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 20px;
}

.preview-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007cba;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    margin: 10px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Hide/show image API settings based on checkbox */
#image_api_settings {
    margin-left: 25px;
}

#backdate_settings {
    margin-left: 25px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Update active tab
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Show/hide content
        $('.smartwriter-tab-content').removeClass('active');
        $(target).addClass('active');
    });
    
    // API provider change
    $('#api_provider').on('change', function() {
        var provider = $(this).val();
        $('.api-provider-instructions').hide();
        $('.api-provider-instructions[data-provider="' + provider + '"]').show();
    });
    
    // Test API connection
    $('#test-api-connection').on('click', function() {
        var apiKey = $('#api_key').val();
        if (!apiKey) {
            alert('<?php _e('Please enter an API key first.', 'smartwriter-ai'); ?>');
            return;
        }
        
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Testing...', 'smartwriter-ai'); ?>').prop('disabled', true);
        
        $.post(ajaxurl, {
            action: 'smartwriter_test_api',
            nonce: smartwriterAjax.nonce,
            api_key: apiKey
        }, function(response) {
            if (response.success) {
                $('#api-test-result').html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
            } else {
                $('#api-test-result').html('<div class="notice notice-error inline"><p>' + response.data + '</p></div>');
            }
        }).always(function() {
            $button.text(originalText).prop('disabled', false);
        });
    });
    
    // Preview content
    $('#preview-content').on('click', function() {
        var prompt = $('#prompt_template').val();
        if (!prompt) {
            alert('<?php _e('Please enter a prompt template first.', 'smartwriter-ai'); ?>');
            return;
        }
        
        $('#preview-loading').show();
        $('#preview-content').empty();
        
        $.post(ajaxurl, {
            action: 'smartwriter_preview_content',
            nonce: smartwriterAjax.nonce,
            prompt: prompt
        }, function(response) {
            $('#preview-loading').hide();
            
            if (response.success) {
                var content = response.data;
                var html = '<div class="preview-result">';
                html += '<h4>' + content.title + '</h4>';
                html += '<p><strong>Meta Description:</strong> ' + content.meta_description + '</p>';
                html += '<p><strong>Focus Keyword:</strong> ' + content.focus_keyword + '</p>';
                if (content.tags && content.tags.length > 0) {
                    html += '<p><strong>Tags:</strong> ' + content.tags.join(', ') + '</p>';
                }
                html += '<div class="content-preview"><strong>Content Preview:</strong><br>' + content.content.substring(0, 500) + '...</div>';
                html += '</div>';
                $('#preview-content').html(html);
            } else {
                $('#preview-content').html('<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>');
            }
        });
    });
    
    // Preview settings modal
    $('#preview-settings').on('click', function() {
        $('#settings-preview-modal').show();
        $('#preview-settings').trigger('click');
    });
    
    // Modal close
    $('.modal-close, .smartwriter-modal').on('click', function(e) {
        if (e.target === this) {
            $('.smartwriter-modal').hide();
        }
    });
    
    // Toggle image API settings
    $('#generate_images_toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#image_api_settings').show();
        } else {
            $('#image_api_settings').hide();
        }
    });
    
    // Toggle backdate settings
    $('#enable_backdate_toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#backdate_settings').show();
        } else {
            $('#backdate_settings').hide();
        }
    });
    
    // Danger zone actions
    $('#clear-all-logs').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to clear all logs? This action cannot be undone.', 'smartwriter-ai'); ?>')) {
            $.post(ajaxurl, {
                action: 'smartwriter_clear_logs',
                nonce: smartwriterAjax.nonce
            }, function(response) {
                if (response.success) {
                    alert('<?php _e('All logs have been cleared.', 'smartwriter-ai'); ?>');
                } else {
                    alert('<?php _e('Failed to clear logs:', 'smartwriter-ai'); ?> ' + response.data);
                }
            });
        }
    });
    
    $('#reset-settings').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to reset all settings to default? This action cannot be undone.', 'smartwriter-ai'); ?>')) {
            if (confirm('<?php _e('This will reset ALL your configuration. Are you absolutely sure?', 'smartwriter-ai'); ?>')) {
                $.post(ajaxurl, {
                    action: 'smartwriter_reset_settings',
                    nonce: smartwriterAjax.nonce
                }, function(response) {
                    if (response.success) {
                        alert('<?php _e('Settings have been reset to default.', 'smartwriter-ai'); ?>');
                        location.reload();
                    } else {
                        alert('<?php _e('Failed to reset settings:', 'smartwriter-ai'); ?> ' + response.data);
                    }
                });
            }
        }
    });
    
    $('#export-settings').on('click', function() {
        $.post(ajaxurl, {
            action: 'smartwriter_export_settings',
            nonce: smartwriterAjax.nonce
        }, function(response) {
            if (response.success) {
                // Create download
                var blob = new Blob([response.data], {type: 'application/json'});
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'smartwriter-ai-settings.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } else {
                alert('<?php _e('Failed to export settings:', 'smartwriter-ai'); ?> ' + response.data);
            }
        });
    });
});
</script>