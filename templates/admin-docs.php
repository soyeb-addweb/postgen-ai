<?php
/**
 * Admin Documentation Template
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Get current settings for examples
$settings = get_option('smartwriter_ai_settings', []);
$api_configured = !empty($settings['api_key']);
?>

<div class="wrap smartwriter-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-book-alt"></span>
        <?php esc_html_e('Documentation & User Guide', 'smartwriter-ai'); ?>
    </h1>

    <div class="smartwriter-docs-container">
        <!-- Navigation Tabs -->
        <div class="docs-navigation" style="margin: 20px 0; border-bottom: 1px solid #ccd0d4;">
            <nav class="docs-tabs" style="display: flex; gap: 0;">
                <button type="button" class="docs-tab active" data-tab="getting-started" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid #2271b1;
                    font-weight: 600;
                ">
                    <?php _e('Getting Started', 'smartwriter-ai'); ?>
                </button>
                <button type="button" class="docs-tab" data-tab="api-setup" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid transparent;
                ">
                    <?php _e('API Setup', 'smartwriter-ai'); ?>
                </button>
                <button type="button" class="docs-tab" data-tab="configuration" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid transparent;
                ">
                    <?php _e('Configuration', 'smartwriter-ai'); ?>
                </button>
                <button type="button" class="docs-tab" data-tab="advanced" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid transparent;
                ">
                    <?php _e('Advanced Features', 'smartwriter-ai'); ?>
                </button>
                <button type="button" class="docs-tab" data-tab="troubleshooting" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid transparent;
                ">
                    <?php _e('Troubleshooting', 'smartwriter-ai'); ?>
                </button>
                <button type="button" class="docs-tab" data-tab="faq" style="
                    padding: 12px 20px; 
                    border: none; 
                    background: #f0f0f1; 
                    cursor: pointer;
                    border-bottom: 3px solid transparent;
                ">
                    <?php _e('FAQ', 'smartwriter-ai'); ?>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="docs-content">
            
            <!-- Getting Started Tab -->
            <div id="getting-started" class="docs-tab-content active">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-flag"></span>
                        <?php _e('Welcome to SmartWriter AI', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        <p style="font-size: 16px; margin-bottom: 20px;">
                            <?php _e('SmartWriter AI is a powerful WordPress plugin that automates blog post creation using artificial intelligence. This guide will help you get started quickly and make the most of all features.', 'smartwriter-ai'); ?>
                        </p>

                        <div class="setup-steps">
                            <h3><?php _e('Quick Setup (5 Minutes)', 'smartwriter-ai'); ?></h3>
                            
                            <div class="step-item" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div class="step-number" style="
                                    width: 30px; 
                                    height: 30px; 
                                    background: <?php echo $api_configured ? '#4caf50' : '#2271b1'; ?>; 
                                    color: white; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center; 
                                    font-weight: bold; 
                                    margin-right: 15px;
                                    flex-shrink: 0;
                                ">
                                    <?php echo $api_configured ? '✓' : '1'; ?>
                                </div>
                                <div class="step-content">
                                    <h4 style="margin: 0 0 8px 0;"><?php _e('Configure API Connection', 'smartwriter-ai'); ?></h4>
                                    <p style="margin: 0 0 10px 0;"><?php _e('Set up your AI API key (Perplexity, OpenAI, or Anthropic) to enable content generation.', 'smartwriter-ai'); ?></p>
                                    <?php if (!$api_configured): ?>
                                        <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings&tab=api'); ?>" class="button button-primary">
                                            <?php _e('Configure API Now', 'smartwriter-ai'); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #4caf50; font-weight: bold;">✓ <?php _e('Completed', 'smartwriter-ai'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="step-item" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div class="step-number" style="
                                    width: 30px; 
                                    height: 30px; 
                                    background: #2271b1; 
                                    color: white; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center; 
                                    font-weight: bold; 
                                    margin-right: 15px;
                                    flex-shrink: 0;
                                ">2</div>
                                <div class="step-content">
                                    <h4 style="margin: 0 0 8px 0;"><?php _e('Customize Content Settings', 'smartwriter-ai'); ?></h4>
                                    <p style="margin: 0 0 10px 0;"><?php _e('Set up your prompt template, default categories, and publishing preferences.', 'smartwriter-ai'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings&tab=content'); ?>" class="button">
                                        <?php _e('Configure Content', 'smartwriter-ai'); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="step-item" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div class="step-number" style="
                                    width: 30px; 
                                    height: 30px; 
                                    background: #2271b1; 
                                    color: white; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center; 
                                    font-weight: bold; 
                                    margin-right: 15px;
                                    flex-shrink: 0;
                                ">3</div>
                                <div class="step-content">
                                    <h4 style="margin: 0 0 8px 0;"><?php _e('Set Posting Schedule', 'smartwriter-ai'); ?></h4>
                                    <p style="margin: 0 0 10px 0;"><?php _e('Configure automatic posting intervals, daily limits, and time windows.', 'smartwriter-ai'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings&tab=scheduling'); ?>" class="button">
                                        <?php _e('Setup Schedule', 'smartwriter-ai'); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="step-item" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <div class="step-number" style="
                                    width: 30px; 
                                    height: 30px; 
                                    background: #2271b1; 
                                    color: white; 
                                    border-radius: 50%; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center; 
                                    font-weight: bold; 
                                    margin-right: 15px;
                                    flex-shrink: 0;
                                ">4</div>
                                <div class="step-content">
                                    <h4 style="margin: 0 0 8px 0;"><?php _e('Test & Monitor', 'smartwriter-ai'); ?></h4>
                                    <p style="margin: 0 0 10px 0;"><?php _e('Generate a test post and monitor the system using the dashboard and logs.', 'smartwriter-ai'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-scheduler'); ?>" class="button">
                                        <?php _e('Schedule Test Post', 'smartwriter-ai'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="feature-highlights" style="margin-top: 30px;">
                            <h3><?php _e('Key Features', 'smartwriter-ai'); ?></h3>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 15px;">
                                <div class="feature-card" style="padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0; color: #2271b1;">
                                        <span class="dashicons dashicons-robot" style="margin-right: 8px;"></span>
                                        <?php _e('AI-Powered Content', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 14px;">
                                        <?php _e('Generate high-quality blog posts using Perplexity AI, OpenAI GPT, or Anthropic Claude.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>

                                <div class="feature-card" style="padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0; color: #2271b1;">
                                        <span class="dashicons dashicons-calendar-alt" style="margin-right: 8px;"></span>
                                        <?php _e('Smart Scheduling', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 14px;">
                                        <?php _e('Automatic post scheduling with custom intervals, time windows, and backdate support.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>

                                <div class="feature-card" style="padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0; color: #2271b1;">
                                        <span class="dashicons dashicons-search" style="margin-right: 8px;"></span>
                                        <?php _e('SEO Optimization', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 14px;">
                                        <?php _e('Automatic SEO meta data, focus keywords, and integration with Yoast, AIOSEO, RankMath.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>

                                <div class="feature-card" style="padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0; color: #2271b1;">
                                        <span class="dashicons dashicons-images-alt2" style="margin-right: 8px;"></span>
                                        <?php _e('Featured Images', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 14px;">
                                        <?php _e('Automatic featured image generation using Unsplash, Pexels, or DALL-E integration.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Setup Tab -->
            <div id="api-setup" class="docs-tab-content" style="display: none;">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <?php _e('API Setup Guide', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        <p style="font-size: 16px; margin-bottom: 25px;">
                            <?php _e('SmartWriter AI supports multiple AI providers. Choose the one that best fits your needs and budget.', 'smartwriter-ai'); ?>
                        </p>

                        <!-- Perplexity AI Setup -->
                        <div class="api-provider-section" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                            <h3 style="margin-top: 0; color: #2271b1;">
                                <span class="dashicons dashicons-star-filled"></span>
                                <?php _e('Perplexity AI (Recommended)', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <div class="provider-info" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                                <p><strong><?php _e('Why Perplexity AI?', 'smartwriter-ai'); ?></strong></p>
                                <ul style="margin-left: 20px;">
                                    <li><?php _e('Real-time web search capabilities for current information', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Competitive pricing and fast response times', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Excellent for news, trends, and up-to-date content', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Multiple model options (Small, Large, Huge)', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>

                            <h4><?php _e('Setup Steps:', 'smartwriter-ai'); ?></h4>
                            <ol style="margin-left: 20px;">
                                <li>
                                    <?php _e('Visit', 'smartwriter-ai'); ?> 
                                    <a href="https://perplexity.ai" target="_blank">https://perplexity.ai</a>
                                    <?php _e('and create an account', 'smartwriter-ai'); ?>
                                </li>
                                <li><?php _e('Go to Settings → API Keys in your Perplexity dashboard', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Generate a new API key', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Copy the API key and paste it in the plugin settings', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Select your preferred model (llama-3.1-sonar-small-128k-online is recommended)', 'smartwriter-ai'); ?></li>
                            </ol>

                            <div class="pricing-info" style="background: #fff3e0; padding: 10px; border-radius: 4px; margin-top: 15px;">
                                <p style="margin: 0;"><strong><?php _e('Pricing:', 'smartwriter-ai'); ?></strong> <?php _e('Starting from $0.20 per 1M tokens (~$5-10/month for typical usage)', 'smartwriter-ai'); ?></p>
                            </div>
                        </div>

                        <!-- OpenAI Setup -->
                        <div class="api-provider-section" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                            <h3 style="margin-top: 0; color: #2271b1;">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php _e('OpenAI (GPT Models)', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <div class="provider-info" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                                <p><strong><?php _e('Best For:', 'smartwriter-ai'); ?></strong></p>
                                <ul style="margin-left: 20px;">
                                    <li><?php _e('Creative writing and storytelling', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('High-quality, coherent long-form content', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Technical documentation and tutorials', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('DALL-E integration for AI-generated images', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>

                            <h4><?php _e('Setup Steps:', 'smartwriter-ai'); ?></h4>
                            <ol style="margin-left: 20px;">
                                <li>
                                    <?php _e('Visit', 'smartwriter-ai'); ?> 
                                    <a href="https://platform.openai.com" target="_blank">https://platform.openai.com</a>
                                    <?php _e('and create an account', 'smartwriter-ai'); ?>
                                </li>
                                <li><?php _e('Navigate to API Keys section', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Create a new secret key', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Add billing information (required for API usage)', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Copy the API key and configure it in the plugin', 'smartwriter-ai'); ?></li>
                            </ol>

                            <div class="pricing-info" style="background: #fff3e0; padding: 10px; border-radius: 4px; margin-top: 15px;">
                                <p style="margin: 0;"><strong><?php _e('Pricing:', 'smartwriter-ai'); ?></strong> <?php _e('GPT-4o: $15/1M input tokens, GPT-3.5: $3/1M tokens', 'smartwriter-ai'); ?></p>
                            </div>
                        </div>

                        <!-- Anthropic Setup -->
                        <div class="api-provider-section" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                            <h3 style="margin-top: 0; color: #2271b1;">
                                <span class="dashicons dashicons-welcome-learn-more"></span>
                                <?php _e('Anthropic Claude', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <div class="provider-info" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                                <p><strong><?php _e('Best For:', 'smartwriter-ai'); ?></strong></p>
                                <ul style="margin-left: 20px;">
                                    <li><?php _e('Detailed analysis and research content', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Safety-focused content generation', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Long-form articles and reports', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Educational and informational content', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>

                            <h4><?php _e('Setup Steps:', 'smartwriter-ai'); ?></h4>
                            <ol style="margin-left: 20px;">
                                <li>
                                    <?php _e('Visit', 'smartwriter-ai'); ?> 
                                    <a href="https://console.anthropic.com" target="_blank">https://console.anthropic.com</a>
                                    <?php _e('and create an account', 'smartwriter-ai'); ?>
                                </li>
                                <li><?php _e('Go to API Keys section', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Generate a new API key', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Set up billing (pay-as-you-go)', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Configure the API key in plugin settings', 'smartwriter-ai'); ?></li>
                            </ol>

                            <div class="pricing-info" style="background: #fff3e0; padding: 10px; border-radius: 4px; margin-top: 15px;">
                                <p style="margin: 0;"><strong><?php _e('Pricing:', 'smartwriter-ai'); ?></strong> <?php _e('Claude 3.5 Sonnet: $15/1M input tokens', 'smartwriter-ai'); ?></p>
                            </div>
                        </div>

                        <!-- Security Tips -->
                        <div class="security-tips" style="background: #ffe6e6; padding: 20px; border-radius: 8px; border: 1px solid #ff9999;">
                            <h3 style="margin-top: 0; color: #d63384;">
                                <span class="dashicons dashicons-shield"></span>
                                <?php _e('Security Best Practices', 'smartwriter-ai'); ?>
                            </h3>
                            <ul style="margin-left: 20px;">
                                <li><?php _e('Never share your API keys publicly or in support forums', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Store API keys securely and rotate them regularly', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Monitor your API usage and set billing alerts', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Use environment variables for API keys in staging/development', 'smartwriter-ai'); ?></li>
                                <li><?php _e('Revoke and regenerate keys if your site is compromised', 'smartwriter-ai'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Tab -->
            <div id="configuration" class="docs-tab-content" style="display: none;">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Configuration Guide', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        
                        <!-- Content Settings -->
                        <div class="config-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('Content Settings', 'smartwriter-ai'); ?></h3>
                            
                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Prompt Template', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('The prompt template is the instruction given to the AI. Use placeholders to make it dynamic:', 'smartwriter-ai'); ?></p>
                                
                                <div class="code-example" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 10px 0; font-family: monospace;">
                                    <strong><?php _e('Example Template:', 'smartwriter-ai'); ?></strong><br>
                                    Write a comprehensive blog post about {topic}. The post should be 800-1000 words, include an engaging title, meta description, and relevant tags. Format as JSON with keys: title, content, meta_description, tags, focus_keyword.
                                </div>

                                <p><strong><?php _e('Available Placeholders:', 'smartwriter-ai'); ?></strong></p>
                                <ul style="margin-left: 20px;">
                                    <li><code>{topic}</code> - <?php _e('Dynamically generated or user-specified topic', 'smartwriter-ai'); ?></li>
                                    <li><code>{date}</code> - <?php _e('Current date', 'smartwriter-ai'); ?></li>
                                    <li><code>{keyword}</code> - <?php _e('Focus keyword for SEO', 'smartwriter-ai'); ?></li>
                                    <li><code>{category}</code> - <?php _e('Selected category name', 'smartwriter-ai'); ?></li>
                                    <li><code>{author}</code> - <?php _e('Post author name', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>

                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Auto-Publish vs Draft', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('Choose whether generated posts should be:', 'smartwriter-ai'); ?></p>
                                <ul style="margin-left: 20px;">
                                    <li><strong><?php _e('Auto-Publish:', 'smartwriter-ai'); ?></strong> <?php _e('Posts go live immediately (recommended for trusted prompts)', 'smartwriter-ai'); ?></li>
                                    <li><strong><?php _e('Save as Draft:', 'smartwriter-ai'); ?></strong> <?php _e('Review before publishing (recommended for new setups)', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Scheduling Settings -->
                        <div class="config-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('Scheduling Settings', 'smartwriter-ai'); ?></h3>
                            
                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Posting Frequency', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('Control how often new posts are generated:', 'smartwriter-ai'); ?></p>
                                
                                <table class="widefat" style="margin: 10px 0;">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Interval', 'smartwriter-ai'); ?></th>
                                            <th><?php _e('Best For', 'smartwriter-ai'); ?></th>
                                            <th><?php _e('Posts/Day', 'smartwriter-ai'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php _e('Every 30 minutes', 'smartwriter-ai'); ?></td>
                                            <td><?php _e('News sites, high-volume blogs', 'smartwriter-ai'); ?></td>
                                            <td>48</td>
                                        </tr>
                                        <tr>
                                            <td><?php _e('Every hour', 'smartwriter-ai'); ?></td>
                                            <td><?php _e('Active blogs, content marketing', 'smartwriter-ai'); ?></td>
                                            <td>24</td>
                                        </tr>
                                        <tr>
                                            <td><?php _e('Every 4 hours', 'smartwriter-ai'); ?></td>
                                            <td><?php _e('Regular blogs, balanced approach', 'smartwriter-ai'); ?></td>
                                            <td>6</td>
                                        </tr>
                                        <tr>
                                            <td><?php _e('Daily', 'smartwriter-ai'); ?></td>
                                            <td><?php _e('Personal blogs, steady content', 'smartwriter-ai'); ?></td>
                                            <td>1</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Time Windows', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('Set specific hours when posts should be published to optimize engagement:', 'smartwriter-ai'); ?></p>
                                <ul style="margin-left: 20px;">
                                    <li><strong><?php _e('Business Hours:', 'smartwriter-ai'); ?></strong> 9:00 AM - 5:00 PM</li>
                                    <li><strong><?php _e('Peak Engagement:', 'smartwriter-ai'); ?></strong> 10:00 AM - 3:00 PM</li>
                                    <li><strong><?php _e('Evening Readers:', 'smartwriter-ai'); ?></strong> 6:00 PM - 9:00 PM</li>
                                    <li><strong><?php _e('24/7 (Global):', 'smartwriter-ai'); ?></strong> <?php _e('No time restrictions', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>

                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Backdating Posts', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('Fill content gaps by generating posts with past publication dates:', 'smartwriter-ai'); ?></p>
                                <div class="info-box" style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin: 10px 0;">
                                    <p style="margin: 0;"><strong><?php _e('Use Cases:', 'smartwriter-ai'); ?></strong></p>
                                    <ul style="margin: 10px 0 0 20px;">
                                        <li><?php _e('New blog - create content history', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Seasonal content - fill previous years', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Topic coverage - comprehensive archive', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="config-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('SEO Integration', 'smartwriter-ai'); ?></h3>
                            
                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('Supported SEO Plugins', 'smartwriter-ai'); ?></h4>
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                    <div class="seo-plugin" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;">Yoast SEO</h5>
                                        <p style="margin: 0; font-size: 13px;">
                                            <?php _e('Automatic meta title, description, focus keyword, and readability optimization.', 'smartwriter-ai'); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="seo-plugin" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;">All in One SEO</h5>
                                        <p style="margin: 0; font-size: 13px;">
                                            <?php _e('Full SEO meta data integration with AIOSEO\'s custom fields and settings.', 'smartwriter-ai'); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="seo-plugin" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;">RankMath</h5>
                                        <p style="margin: 0; font-size: 13px;">
                                            <?php _e('Complete RankMath integration including focus keywords and social meta.', 'smartwriter-ai'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="setting-item" style="margin-bottom: 20px;">
                                <h4><?php _e('SEO Best Practices', 'smartwriter-ai'); ?></h4>
                                <ul style="margin-left: 20px;">
                                    <li><?php _e('Include focus keywords in your prompt template', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Request meta descriptions under 160 characters', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Ask for 3-5 relevant tags per post', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Use structured output (JSON) for consistent formatting', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Review generated content for keyword optimization', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Features Tab -->
            <div id="advanced" class="docs-tab-content" style="display: none;">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-admin-tools"></span>
                        <?php _e('Advanced Features', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        
                        <!-- Featured Images -->
                        <div class="advanced-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;">
                                <span class="dashicons dashicons-format-image"></span>
                                <?php _e('Automatic Featured Images', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <p><?php _e('SmartWriter AI can automatically generate or source featured images for your posts using multiple services:', 'smartwriter-ai'); ?></p>

                            <div class="image-services" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                                <div class="service-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0;">Unsplash</h4>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                                        <li><?php _e('High-quality stock photos', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Free tier available', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('keyword-based search', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Commercial use allowed', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0;">Pexels</h4>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                                        <li><?php _e('Diverse photo collection', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Free API access', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Excellent for lifestyle content', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('No attribution required', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 style="margin: 0 0 10px 0;">DALL-E</h4>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                                        <li><?php _e('AI-generated unique images', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Custom image creation', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Perfect content match', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Higher cost per image', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="setup-instructions" style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <h4><?php _e('Setup Instructions:', 'smartwriter-ai'); ?></h4>
                                <ol>
                                    <li><?php _e('Enable "Generate Featured Images" in Advanced Settings', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Choose your preferred image service', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Add the respective API key (Unsplash/Pexels/OpenAI)', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Images will be automatically selected based on post content', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>
                        </div>

                        <!-- Bulk Operations -->
                        <div class="advanced-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;">
                                <span class="dashicons dashicons-list-view"></span>
                                <?php _e('Bulk Content Generation', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <p><?php _e('Generate multiple posts efficiently using the bulk scheduling feature:', 'smartwriter-ai'); ?></p>

                            <div class="bulk-strategies" style="margin: 20px 0;">
                                <h4><?php _e('Content Strategies:', 'smartwriter-ai'); ?></h4>
                                
                                <div class="strategy" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong><?php _e('Topic Series:', 'smartwriter-ai'); ?></strong>
                                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                                        <?php _e('Create related posts on a theme: "WordPress Security Tips", "WordPress Performance", "WordPress SEO", etc.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>

                                <div class="strategy" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong><?php _e('Seasonal Content:', 'smartwriter-ai'); ?></strong>
                                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                                        <?php _e('Generate holiday-themed content, seasonal tips, or time-sensitive topics distributed across relevant dates.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>

                                <div class="strategy" style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong><?php _e('Historical Content:', 'smartwriter-ai'); ?></strong>
                                    <p style="margin: 5px 0 0 0; font-size: 13px;">
                                        <?php _e('Use backdating to create an archive of evergreen content, building domain authority and search presence.', 'smartwriter-ai'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="bulk-tips" style="background: #e7f3ff; padding: 15px; border-radius: 6px;">
                                <h4 style="margin-top: 0;"><?php _e('Bulk Generation Tips:', 'smartwriter-ai'); ?></h4>
                                <ul style="margin-left: 20px;">
                                    <li><?php _e('Start with 5-10 posts to test your setup', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Use varied topics to avoid content similarity', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Spread posts across multiple days for natural publishing', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Monitor logs during bulk operations', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Consider API rate limits and costs', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Custom Hooks -->
                        <div class="advanced-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <?php _e('Developer Hooks & Filters', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <p><?php _e('Customize plugin behavior using WordPress hooks (for developers):', 'smartwriter-ai'); ?></p>

                            <div class="code-examples">
                                <h4><?php _e('Content Filters:', 'smartwriter-ai'); ?></h4>
                                <div class="code-example" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 10px 0; font-family: monospace; font-size: 12px;">
                                    <strong>// Modify content before publishing</strong><br>
                                    add_filter('smartwriter_pre_publish_content', function($content, $post_data) {<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;// Add custom footer to all AI posts<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;$content .= "\n\n<em>Generated by SmartWriter AI</em>";<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;return $content;<br>
                                    }, 10, 2);
                                </div>

                                <h4><?php _e('Action Hooks:', 'smartwriter-ai'); ?></h4>
                                <div class="code-example" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 10px 0; font-family: monospace; font-size: 12px;">
                                    <strong>// After post is created</strong><br>
                                    add_action('smartwriter_post_created', function($post_id, $ai_data) {<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;// Send notification, update analytics, etc.<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;wp_mail('admin@site.com', 'New AI Post', 'Post created: ' . $post_id);<br>
                                    }, 10, 2);
                                </div>

                                <div class="available-hooks" style="background: #fff3e0; padding: 15px; border-radius: 6px; margin: 15px 0;">
                                    <h4 style="margin-top: 0;"><?php _e('Available Hooks:', 'smartwriter-ai'); ?></h4>
                                    <ul style="margin-left: 20px; font-family: monospace; font-size: 12px;">
                                        <li>smartwriter_pre_api_request</li>
                                        <li>smartwriter_post_api_request</li>
                                        <li>smartwriter_pre_publish_content</li>
                                        <li>smartwriter_post_created</li>
                                        <li>smartwriter_scheduler_run</li>
                                        <li>smartwriter_error_occurred</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Optimization -->
                        <div class="advanced-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;">
                                <span class="dashicons dashicons-performance"></span>
                                <?php _e('Performance Optimization', 'smartwriter-ai'); ?>
                            </h3>
                            
                            <div class="optimization-tips">
                                <h4><?php _e('Server Requirements:', 'smartwriter-ai'); ?></h4>
                                <ul style="margin-left: 20px;">
                                    <li><strong>PHP 7.4+</strong> <?php _e('(8.0+ recommended for better performance)', 'smartwriter-ai'); ?></li>
                                    <li><strong>Memory:</strong> <?php _e('256MB minimum, 512MB recommended', 'smartwriter-ai'); ?></li>
                                    <li><strong>Execution Time:</strong> <?php _e('60 seconds minimum for API calls', 'smartwriter-ai'); ?></li>
                                    <li><strong>CRON:</strong> <?php _e('Reliable WordPress cron or server cron', 'smartwriter-ai'); ?></li>
                                </ul>

                                <h4><?php _e('Optimization Strategies:', 'smartwriter-ai'); ?></h4>
                                <div class="optimization-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 15px 0;">
                                    <div class="optimization-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;"><?php _e('API Optimization', 'smartwriter-ai'); ?></h5>
                                        <ul style="margin: 0; font-size: 13px; padding-left: 15px;">
                                            <li><?php _e('Use smaller AI models for faster responses', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Cache API responses when possible', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Monitor API rate limits', 'smartwriter-ai'); ?></li>
                                        </ul>
                                    </div>

                                    <div class="optimization-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;"><?php _e('Database Optimization', 'smartwriter-ai'); ?></h5>
                                        <ul style="margin: 0; font-size: 13px; padding-left: 15px;">
                                            <li><?php _e('Regular log cleanup (automatic)', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Index optimization for queries', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Monitor table growth', 'smartwriter-ai'); ?></li>
                                        </ul>
                                    </div>

                                    <div class="optimization-card" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                                        <h5 style="margin: 0 0 8px 0;"><?php _e('Scheduling Optimization', 'smartwriter-ai'); ?></h5>
                                        <ul style="margin: 0; font-size: 13px; padding-left: 15px;">
                                            <li><?php _e('Balanced posting intervals', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Avoid peak server hours', 'smartwriter-ai'); ?></li>
                                            <li><?php _e('Distribute bulk operations', 'smartwriter-ai'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Tab -->
            <div id="troubleshooting" class="docs-tab-content" style="display: none;">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-sos"></span>
                        <?php _e('Troubleshooting Guide', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        
                        <!-- Common Issues -->
                        <div class="troubleshooting-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('Common Issues & Solutions', 'smartwriter-ai'); ?></h3>
                            
                            <div class="issue-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                <h4 style="margin: 0 0 10px 0; color: #d63384;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('API Connection Failed', 'smartwriter-ai'); ?>
                                </h4>
                                <p><strong><?php _e('Symptoms:', 'smartwriter-ai'); ?></strong> <?php _e('Test connection fails, no posts generated', 'smartwriter-ai'); ?></p>
                                <p><strong><?php _e('Solutions:', 'smartwriter-ai'); ?></strong></p>
                                <ol style="margin-left: 20px;">
                                    <li><?php _e('Verify API key is correct and active', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check API provider billing and limits', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Ensure server can make external HTTPS requests', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check firewall and proxy settings', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Try different API provider', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>

                            <div class="issue-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                <h4 style="margin: 0 0 10px 0; color: #d63384;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('Posts Not Publishing', 'smartwriter-ai'); ?>
                                </h4>
                                <p><strong><?php _e('Symptoms:', 'smartwriter-ai'); ?></strong> <?php _e('Scheduled posts remain in pending status', 'smartwriter-ai'); ?></p>
                                <p><strong><?php _e('Solutions:', 'smartwriter-ai'); ?></strong></p>
                                <ol style="margin-left: 20px;">
                                    <li><?php _e('Check WordPress CRON is functioning', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Verify posting time window settings', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check daily post limits', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Review error logs for specific issues', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Try manual "Force Run" to test', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>

                            <div class="issue-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                <h4 style="margin: 0 0 10px 0; color: #d63384;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('Poor Content Quality', 'smartwriter-ai'); ?>
                                </h4>
                                <p><strong><?php _e('Symptoms:', 'smartwriter-ai'); ?></strong> <?php _e('Generated content is off-topic, too short, or low quality', 'smartwriter-ai'); ?></p>
                                <p><strong><?php _e('Solutions:', 'smartwriter-ai'); ?></strong></p>
                                <ol style="margin-left: 20px;">
                                    <li><?php _e('Improve prompt template with specific instructions', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Try a different AI model (larger/more capable)', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Add content length requirements to prompt', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Include style and tone guidelines', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Use structured output (JSON) for consistency', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>

                            <div class="issue-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                <h4 style="margin: 0 0 10px 0; color: #d63384;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('Featured Images Not Working', 'smartwriter-ai'); ?>
                                </h4>
                                <p><strong><?php _e('Symptoms:', 'smartwriter-ai'); ?></strong> <?php _e('Posts published without featured images', 'smartwriter-ai'); ?></p>
                                <p><strong><?php _e('Solutions:', 'smartwriter-ai'); ?></strong></p>
                                <ol style="margin-left: 20px;">
                                    <li><?php _e('Verify image API key is valid', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check image API rate limits and quotas', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Ensure WordPress can download external images', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check file upload permissions', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Try different image provider', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>

                            <div class="issue-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                                <h4 style="margin: 0 0 10px 0; color: #d63384;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('SEO Data Missing', 'smartwriter-ai'); ?>
                                </h4>
                                <p><strong><?php _e('Symptoms:', 'smartwriter-ai'); ?></strong> <?php _e('Meta descriptions, keywords not set in SEO plugin', 'smartwriter-ai'); ?></p>
                                <p><strong><?php _e('Solutions:', 'smartwriter-ai'); ?></strong></p>
                                <ol style="margin-left: 20px;">
                                    <li><?php _e('Verify SEO plugin is detected and supported', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Update to latest SEO plugin version', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Check if AI response includes SEO fields', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Use JSON response format for structured data', 'smartwriter-ai'); ?></li>
                                    <li><?php _e('Review SEO integration logs', 'smartwriter-ai'); ?></li>
                                </ol>
                            </div>
                        </div>

                        <!-- Debug Mode -->
                        <div class="troubleshooting-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('Debug Mode & Logging', 'smartwriter-ai'); ?></h3>
                            
                            <div class="debug-info" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 15px 0;">
                                <h4><?php _e('Enable WordPress Debug Mode:', 'smartwriter-ai'); ?></h4>
                                <p><?php _e('Add to wp-config.php for detailed error information:', 'smartwriter-ai'); ?></p>
                                <div class="code-example" style="background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px;">
                                    define('WP_DEBUG', true);<br>
                                    define('WP_DEBUG_LOG', true);<br>
                                    define('WP_DEBUG_DISPLAY', false);
                                </div>
                            </div>

                            <div class="log-locations" style="background: #fff3e0; padding: 15px; border-radius: 6px;">
                                <h4><?php _e('Check Log Files:', 'smartwriter-ai'); ?></h4>
                                <ul style="margin-left: 20px;">
                                    <li><strong><?php _e('Plugin Logs:', 'smartwriter-ai'); ?></strong> <?php _e('Admin → SmartWriter AI → Logs', 'smartwriter-ai'); ?></li>
                                    <li><strong><?php _e('WordPress Debug Log:', 'smartwriter-ai'); ?></strong> /wp-content/debug.log</li>
                                    <li><strong><?php _e('Server Error Log:', 'smartwriter-ai'); ?></strong> <?php _e('Check hosting control panel', 'smartwriter-ai'); ?></li>
                                    <li><strong><?php _e('PHP Error Log:', 'smartwriter-ai'); ?></strong> <?php _e('Usually in public_html or error_log file', 'smartwriter-ai'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- System Health Check -->
                        <div class="troubleshooting-section" style="margin-bottom: 30px;">
                            <h3 style="color: #2271b1;"><?php _e('System Health Check', 'smartwriter-ai'); ?></h3>
                            
                            <div class="health-checklist">
                                <p><?php _e('Use this checklist to verify your system is properly configured:', 'smartwriter-ai'); ?></p>
                                
                                <div class="checklist-items" style="margin: 15px 0;">
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('WordPress CRON is working (check Site Health → Info → CRON)', 'smartwriter-ai'); ?>
                                    </label>
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('Server can make external HTTPS requests', 'smartwriter-ai'); ?>
                                    </label>
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('PHP memory limit is at least 256MB', 'smartwriter-ai'); ?>
                                    </label>
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('API key is valid and has sufficient credits', 'smartwriter-ai'); ?>
                                    </label>
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('WordPress uploads directory is writable', 'smartwriter-ai'); ?>
                                    </label>
                                    <label style="display: block; margin: 8px 0;">
                                        <input type="checkbox" style="margin-right: 8px;">
                                        <?php _e('No conflicting plugins (check plugin conflicts)', 'smartwriter-ai'); ?>
                                    </label>
                                </div>

                                <div class="system-info" style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin: 15px 0;">
                                    <h4><?php _e('System Information:', 'smartwriter-ai'); ?></h4>
                                    <ul style="margin-left: 20px; font-family: monospace; font-size: 12px;">
                                        <li>WordPress Version: <?php echo get_bloginfo('version'); ?></li>
                                        <li>PHP Version: <?php echo phpversion(); ?></li>
                                        <li>Memory Limit: <?php echo ini_get('memory_limit'); ?></li>
                                        <li>Max Execution Time: <?php echo ini_get('max_execution_time'); ?>s</li>
                                        <li>Plugin Version: <?php echo SMARTWRITER_AI_VERSION; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Getting Help -->
                        <div class="troubleshooting-section">
                            <h3 style="color: #2271b1;"><?php _e('Getting Additional Help', 'smartwriter-ai'); ?></h3>
                            
                            <div class="help-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div class="help-option" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px; text-align: center;">
                                    <h4 style="margin: 0 0 8px 0;">
                                        <span class="dashicons dashicons-admin-comments"></span>
                                        <?php _e('Support Forum', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 13px;"><?php _e('Community support and discussions', 'smartwriter-ai'); ?></p>
                                </div>

                                <div class="help-option" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px; text-align: center;">
                                    <h4 style="margin: 0 0 8px 0;">
                                        <span class="dashicons dashicons-book"></span>
                                        <?php _e('Documentation', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 13px;"><?php _e('Comprehensive guides and tutorials', 'smartwriter-ai'); ?></p>
                                </div>

                                <div class="help-option" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px; text-align: center;">
                                    <h4 style="margin: 0 0 8px 0;">
                                        <span class="dashicons dashicons-email"></span>
                                        <?php _e('Premium Support', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 13px;"><?php _e('Priority email support for pro users', 'smartwriter-ai'); ?></p>
                                </div>

                                <div class="help-option" style="padding: 15px; border: 1px solid #ddd; border-radius: 6px; text-align: center;">
                                    <h4 style="margin: 0 0 8px 0;">
                                        <span class="dashicons dashicons-video-alt3"></span>
                                        <?php _e('Video Tutorials', 'smartwriter-ai'); ?>
                                    </h4>
                                    <p style="margin: 0; font-size: 13px;"><?php _e('Step-by-step video guides', 'smartwriter-ai'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Tab -->
            <div id="faq" class="docs-tab-content" style="display: none;">
                <div class="smartwriter-card">
                    <h2>
                        <span class="dashicons dashicons-editor-help"></span>
                        <?php _e('Frequently Asked Questions', 'smartwriter-ai'); ?>
                    </h2>
                    
                    <div style="padding: 20px;">
                        
                        <div class="faq-section">
                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('How much does it cost to run SmartWriter AI?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('The plugin itself is free, but you need an API subscription:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><strong>Perplexity AI:</strong> <?php _e('~$5-10/month for typical usage', 'smartwriter-ai'); ?></li>
                                        <li><strong>OpenAI:</strong> <?php _e('~$10-20/month depending on model and usage', 'smartwriter-ai'); ?></li>
                                        <li><strong>Anthropic:</strong> <?php _e('~$15-25/month for regular use', 'smartwriter-ai'); ?></li>
                                    </ul>
                                    <p><?php _e('Costs depend on post frequency, length, and AI model choice.', 'smartwriter-ai'); ?></p>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Can I edit posts before they\'re published?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Yes! You have several options:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Set posts to save as drafts for manual review', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Use the content preview feature before scheduling', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Edit published posts like any WordPress post', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Will Google penalize AI-generated content?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Google focuses on content quality, not creation method. To stay safe:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Use high-quality prompts for better content', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Review and edit generated content', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Ensure content provides real value to users', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Add personal insights and experiences', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Follow Google\'s helpful content guidelines', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Can I use multiple AI providers at once?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Currently, you can configure one provider at a time. However, you can:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Switch providers anytime in settings', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Test different providers for quality comparison', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Use different providers for different content types', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('How do I ensure content uniqueness?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('AI typically generates unique content, but you can enhance uniqueness by:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Using specific, varied topics in your prompts', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Including unique angles or perspectives', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Adding personal experiences and insights', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Using plagiarism checkers if concerned', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('What happens if the API is down?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('The plugin handles API failures gracefully:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Failed requests are logged for review', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Scheduled posts are retried automatically', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('You\'ll receive error notifications', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('No posts are published with errors', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Can I customize the posting schedule?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Yes, you have full control over scheduling:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Set posting intervals (30 min to daily)', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Define time windows (e.g., 9 AM - 5 PM)', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Set daily post limits', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Schedule individual posts manually', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Backdate posts to fill content gaps', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Does it work with my theme and plugins?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('SmartWriter AI is designed for maximum compatibility:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Works with any WordPress theme', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Compatible with major SEO plugins (Yoast, AIOSEO, RankMath)', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Supports custom post types', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Integrates with page builders', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Works with caching plugins', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('How do I backup my settings?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Your settings are stored in WordPress database and included in:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Standard WordPress database backups', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Plugin export/import feature (coming soon)', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('WordPress export tools', 'smartwriter-ai'); ?></li>
                                    </ul>
                                    <p><?php _e('Settings are preserved when updating the plugin.', 'smartwriter-ai'); ?></p>
                                </div>
                            </div>

                            <div class="faq-item" style="margin-bottom: 20px;">
                                <h3 style="color: #2271b1; cursor: pointer;" class="faq-question">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php _e('Can I use this for multiple websites?', 'smartwriter-ai'); ?>
                                </h3>
                                <div class="faq-answer" style="display: none; margin-left: 20px;">
                                    <p><?php _e('Yes, you can install SmartWriter AI on multiple sites:', 'smartwriter-ai'); ?></p>
                                    <ul>
                                        <li><?php _e('Each site needs its own API key configuration', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Settings are per-site (not network-wide)', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('API costs accumulate across all sites', 'smartwriter-ai'); ?></li>
                                        <li><?php _e('Consider API rate limits for multiple sites', 'smartwriter-ai'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.docs-tab {
    transition: all 0.3s ease;
}

.docs-tab:hover {
    background: #e0e0e0 !important;
}

.docs-tab.active {
    background: white !important;
    border-bottom-color: #2271b1 !important;
}

.docs-tab-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.faq-question {
    transition: color 0.2s ease;
}

.faq-question:hover {
    color: #135e96 !important;
}

.faq-question .dashicons {
    transition: transform 0.2s ease;
}

.faq-question.active .dashicons {
    transform: rotate(90deg);
}

.code-example {
    max-width: 100%;
    overflow-x: auto;
}

.feature-card, .optimization-card, .help-option {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.feature-card:hover, .optimization-card:hover, .help-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.docs-tab').on('click', function() {
        var tabId = $(this).data('tab');
        
        // Update tab appearance
        $('.docs-tab').removeClass('active').css({
            'background': '#f0f0f1',
            'border-bottom-color': 'transparent'
        });
        $(this).addClass('active').css({
            'background': 'white',
            'border-bottom-color': '#2271b1'
        });
        
        // Show corresponding content
        $('.docs-tab-content').hide();
        $('#' + tabId).show();
    });
    
    // FAQ toggle
    $('.faq-question').on('click', function() {
        var $answer = $(this).siblings('.faq-answer');
        var $icon = $(this).find('.dashicons');
        
        if ($answer.is(':visible')) {
            $answer.slideUp();
            $icon.removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
            $(this).removeClass('active');
        } else {
            $answer.slideDown();
            $icon.removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
            $(this).addClass('active');
        }
    });
    
    // Direct tab linking (if URL has tab parameter)
    var urlParams = new URLSearchParams(window.location.search);
    var tab = urlParams.get('tab');
    if (tab) {
        $('.docs-tab[data-tab="' + tab + '"]').click();
    }
    
    // Auto-expand first FAQ item
    $('.faq-question').first().click();
});
</script>