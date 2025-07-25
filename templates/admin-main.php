<?php
/**
 * Admin Main Dashboard Template
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Get instances
$logger = new SmartWriter_Logger();
$scheduler = new SmartWriter_Scheduler();
$post_creator = new SmartWriter_Post_Creator();
$api_connector = new SmartWriter_API_Connector();

// Get dashboard data
$dashboard_data = $logger->get_dashboard_data();
$scheduler_status = $scheduler->get_scheduler_status();
$settings = get_option('smartwriter_ai_settings', []);
$ai_posts = $post_creator->get_ai_generated_posts(5);

// Calculate stats
$posts_this_month = get_posts([
    'post_type' => 'post',
    'meta_query' => [
        ['key' => '_smartwriter_ai_generated', 'value' => '1', 'compare' => '=']
    ],
    'date_query' => [
        ['year' => date('Y'), 'month' => date('m')]
    ],
    'fields' => 'ids'
]);
?>

<div class="wrap smartwriter-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-edit-large"></span>
        <?php esc_html_e('SmartWriter AI Dashboard', 'smartwriter-ai'); ?>
    </h1>
    
    <div class="smartwriter-version">
        <?php printf(__('Version %s', 'smartwriter-ai'), SMARTWRITER_AI_VERSION); ?>
    </div>

    <!-- System Health Alert -->
    <?php if ($dashboard_data['health']['score'] < 60): ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('System Health Warning:', 'smartwriter-ai'); ?></strong>
                <?php _e('Your SmartWriter AI system health score is below optimal.', 'smartwriter-ai'); ?>
                <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-logs'); ?>" class="button button-small">
                    <?php _e('View Logs', 'smartwriter-ai'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

    <!-- Quick Stats Cards -->
    <div class="smartwriter-stats-grid">
        <div class="smartwriter-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo count($posts_this_month); ?></h3>
                <p><?php _e('Posts This Month', 'smartwriter-ai'); ?></p>
            </div>
        </div>

        <div class="smartwriter-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-heart" style="color: <?php echo $dashboard_data['health']['score'] >= 80 ? '#28a745' : ($dashboard_data['health']['score'] >= 60 ? '#ffc107' : '#dc3545'); ?>"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboard_data['health']['score']; ?>%</h3>
                <p><?php _e('System Health', 'smartwriter-ai'); ?></p>
                <small><?php echo $dashboard_data['health']['status']; ?></small>
            </div>
        </div>

        <div class="smartwriter-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $scheduler_status['posts_today']; ?>/<?php echo $scheduler_status['max_posts_per_day']; ?></h3>
                <p><?php _e('Posts Today', 'smartwriter-ai'); ?></p>
            </div>
        </div>

        <div class="smartwriter-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-admin-tools"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $dashboard_data['stats']['total']; ?></h3>
                <p><?php _e('Log Entries (7 days)', 'smartwriter-ai'); ?></p>
            </div>
        </div>
    </div>

    <div class="smartwriter-dashboard-grid">
        <!-- Left Column -->
        <div class="smartwriter-dashboard-left">
            
            <!-- Scheduler Status -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <?php _e('Scheduler Status', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="scheduler-status">
                    <div class="status-indicator <?php echo $scheduler_status['active'] ? 'active' : 'inactive'; ?>">
                        <span class="indicator-dot"></span>
                        <?php echo $scheduler_status['active'] ? __('Active', 'smartwriter-ai') : __('Inactive', 'smartwriter-ai'); ?>
                    </div>
                    
                    <?php if ($scheduler_status['active']): ?>
                        <div class="scheduler-details">
                            <p>
                                <strong><?php _e('Next Run:', 'smartwriter-ai'); ?></strong>
                                <?php echo $scheduler_status['next_run'] ? date('M j, Y g:i A', strtotime($scheduler_status['next_run'])) : __('Not scheduled', 'smartwriter-ai'); ?>
                            </p>
                            <p>
                                <strong><?php _e('Posting Window:', 'smartwriter-ai'); ?></strong>
                                <?php echo $scheduler_status['posting_window']['start']; ?> - <?php echo $scheduler_status['posting_window']['end']; ?>
                            </p>
                            <p>
                                <strong><?php _e('Interval:', 'smartwriter-ai'); ?></strong>
                                <?php 
                                $intervals = [
                                    'smartwriter_30min' => __('Every 30 minutes', 'smartwriter-ai'),
                                    'hourly' => __('Every hour', 'smartwriter-ai'),
                                    'smartwriter_2hours' => __('Every 2 hours', 'smartwriter-ai'),
                                    'smartwriter_4hours' => __('Every 4 hours', 'smartwriter-ai'),
                                    'smartwriter_6hours' => __('Every 6 hours', 'smartwriter-ai'),
                                    'daily' => __('Daily', 'smartwriter-ai')
                                ];
                                echo $intervals[$scheduler_status['interval']] ?? $scheduler_status['interval'];
                                ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="no-data"><?php _e('Scheduler is not active. Configure your API key and enable auto-publishing in settings.', 'smartwriter-ai'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-controls-play"></span>
                    <?php _e('Quick Actions', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="quick-actions">
                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings'); ?>" class="button button-primary">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Settings', 'smartwriter-ai'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-scheduler'); ?>" class="button button-secondary">
                        <span class="dashicons dashicons-calendar"></span>
                        <?php _e('Schedule Posts', 'smartwriter-ai'); ?>
                    </a>
                    
                    <button type="button" id="test-content-generation" class="button button-secondary">
                        <span class="dashicons dashicons-lightbulb"></span>
                        <?php _e('Test Generation', 'smartwriter-ai'); ?>
                    </button>
                    
                    <button type="button" id="force-run-scheduler" class="button button-secondary">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Force Run', 'smartwriter-ai'); ?>
                    </button>
                </div>
            </div>

            <!-- Recent AI Posts -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Recent AI Posts', 'smartwriter-ai'); ?>
                </h2>
                
                <?php if (!empty($ai_posts)): ?>
                    <div class="recent-posts">
                        <?php foreach ($ai_posts as $post): ?>
                            <div class="post-item">
                                <div class="post-title">
                                    <a href="<?php echo get_edit_post_link($post->ID); ?>" target="_blank">
                                        <?php echo esc_html($post->post_title); ?>
                                    </a>
                                    <span class="post-status status-<?php echo $post->post_status; ?>">
                                        <?php echo ucfirst($post->post_status); ?>
                                    </span>
                                </div>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo date('M j, Y g:i A', strtotime($post->post_date)); ?></span>
                                    <?php
                                    $word_count = get_post_meta($post->ID, '_smartwriter_content_stats', true);
                                    if ($word_count && isset($word_count['word_count'])):
                                    ?>
                                        <span class="word-count"><?php echo $word_count['word_count']; ?> words</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card-footer">
                        <a href="<?php echo admin_url('edit.php?meta_key=_smartwriter_ai_generated&meta_value=1'); ?>" class="view-all-link">
                            <?php _e('View All AI Posts', 'smartwriter-ai'); ?> →
                        </a>
                    </div>
                <?php else: ?>
                    <p class="no-data"><?php _e('No AI-generated posts yet. Configure your settings and start creating content!', 'smartwriter-ai'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="smartwriter-dashboard-right">
            
            <!-- System Health -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-heart"></span>
                    <?php _e('System Health', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="health-score">
                    <div class="score-circle">
                        <div class="score-text">
                            <span class="score-number"><?php echo $dashboard_data['health']['score']; ?></span>
                            <span class="score-label">%</span>
                        </div>
                    </div>
                    <div class="score-status">
                        <h3><?php echo $dashboard_data['health']['status']; ?></h3>
                        <p><?php _e('Last checked:', 'smartwriter-ai'); ?> <?php echo date('g:i A', strtotime($dashboard_data['health']['last_check'])); ?></p>
                    </div>
                </div>
                
                <?php if (!empty($dashboard_data['health']['issues'])): ?>
                    <div class="health-issues">
                        <h4><?php _e('Issues Detected:', 'smartwriter-ai'); ?></h4>
                        <ul>
                            <?php foreach ($dashboard_data['health']['issues'] as $issue): ?>
                                <li><?php echo esc_html($issue); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Log Activity -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Recent Activity', 'smartwriter-ai'); ?>
                </h2>
                
                <?php if (!empty($dashboard_data['recent_logs'])): ?>
                    <div class="recent-logs">
                        <?php 
                        $log_colors = $logger->get_log_level_colors();
                        $log_icons = $logger->get_log_level_icons();
                        foreach ($dashboard_data['recent_logs'] as $log): 
                        ?>
                            <div class="log-item">
                                <div class="log-icon">
                                    <span class="dashicons <?php echo $log_icons[$log->type]; ?>" style="color: <?php echo $log_colors[$log->type]; ?>"></span>
                                </div>
                                <div class="log-content">
                                    <div class="log-message"><?php echo esc_html($log->message); ?></div>
                                    <div class="log-time"><?php echo human_time_diff(strtotime($log->created_at)); ?> ago</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card-footer">
                        <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-logs'); ?>" class="view-all-link">
                            <?php _e('View All Logs', 'smartwriter-ai'); ?> →
                        </a>
                    </div>
                <?php else: ?>
                    <p class="no-data"><?php _e('No recent activity.', 'smartwriter-ai'); ?></p>
                <?php endif; ?>
            </div>

            <!-- Configuration Status -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('Configuration Status', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="config-status">
                    <div class="config-item">
                        <span class="config-label"><?php _e('API Key:', 'smartwriter-ai'); ?></span>
                        <span class="config-value <?php echo !empty($settings['api_key']) ? 'configured' : 'not-configured'; ?>">
                            <?php echo !empty($settings['api_key']) ? __('Configured', 'smartwriter-ai') : __('Not Set', 'smartwriter-ai'); ?>
                        </span>
                    </div>
                    
                    <div class="config-item">
                        <span class="config-label"><?php _e('AI Provider:', 'smartwriter-ai'); ?></span>
                        <span class="config-value"><?php echo ucfirst($settings['api_provider'] ?? 'Not Set'); ?></span>
                    </div>
                    
                    <div class="config-item">
                        <span class="config-label"><?php _e('Auto Publish:', 'smartwriter-ai'); ?></span>
                        <span class="config-value <?php echo !empty($settings['auto_publish']) ? 'enabled' : 'disabled'; ?>">
                            <?php echo !empty($settings['auto_publish']) ? __('Enabled', 'smartwriter-ai') : __('Disabled', 'smartwriter-ai'); ?>
                        </span>
                    </div>
                    
                    <div class="config-item">
                        <span class="config-label"><?php _e('SEO Plugin:', 'smartwriter-ai'); ?></span>
                        <span class="config-value"><?php echo ucfirst($settings['seo_plugin'] ?? 'None'); ?></span>
                    </div>
                    
                    <div class="config-item">
                        <span class="config-label"><?php _e('Featured Images:', 'smartwriter-ai'); ?></span>
                        <span class="config-value <?php echo !empty($settings['generate_images']) ? 'enabled' : 'disabled'; ?>">
                            <?php echo !empty($settings['generate_images']) ? __('Enabled', 'smartwriter-ai') : __('Disabled', 'smartwriter-ai'); ?>
                        </span>
                    </div>
                </div>
                
                <?php if (empty($settings['api_key'])): ?>
                    <div class="config-alert">
                        <p><?php _e('⚠️ Complete your configuration to start generating content automatically.', 'smartwriter-ai'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-settings'); ?>" class="button button-primary button-small">
                            <?php _e('Configure Now', 'smartwriter-ai'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Test Generation Modal -->
<div id="test-generation-modal" class="smartwriter-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Test Content Generation', 'smartwriter-ai'); ?></h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="test-topic"><?php _e('Test Topic:', 'smartwriter-ai'); ?></label>
                <input type="text" id="test-topic" placeholder="<?php _e('Enter a topic to test...', 'smartwriter-ai'); ?>" />
            </div>
            <button type="button" id="run-test-generation" class="button button-primary">
                <?php _e('Generate Test Content', 'smartwriter-ai'); ?>
            </button>
            <div id="test-generation-result"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Test content generation
    $('#test-content-generation').on('click', function() {
        $('#test-generation-modal').show();
    });
    
    $('.modal-close').on('click', function() {
        $('.smartwriter-modal').hide();
    });
    
    $('#run-test-generation').on('click', function() {
        var topic = $('#test-topic').val();
        if (!topic) {
            alert('<?php _e('Please enter a topic to test.', 'smartwriter-ai'); ?>');
            return;
        }
        
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Generating...', 'smartwriter-ai'); ?>').prop('disabled', true);
        
        $.post(ajaxurl, {
            action: 'smartwriter_preview_content',
            nonce: smartwriterAjax.nonce,
            prompt: 'Write a test blog post about ' + topic
        }, function(response) {
            if (response.success) {
                var content = response.data;
                var html = '<div class="test-result-success">';
                html += '<h4>' + content.title + '</h4>';
                html += '<p><strong>Meta Description:</strong> ' + content.meta_description + '</p>';
                html += '<p><strong>Focus Keyword:</strong> ' + content.focus_keyword + '</p>';
                html += '<p><strong>Tags:</strong> ' + (content.tags ? content.tags.join(', ') : 'None') + '</p>';
                html += '<div class="content-preview">' + content.content.substring(0, 300) + '...</div>';
                html += '</div>';
                $('#test-generation-result').html(html);
            } else {
                $('#test-generation-result').html('<div class="test-result-error">Error: ' + response.data + '</div>');
            }
        }).always(function() {
            $button.text(originalText).prop('disabled', false);
        });
    });
    
    // Force run scheduler
    $('#force-run-scheduler').on('click', function() {
        if (!confirm('<?php _e('Are you sure you want to force run the scheduler?', 'smartwriter-ai'); ?>')) {
            return;
        }
        
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Running...', 'smartwriter-ai'); ?>').prop('disabled', true);
        
        $.post(ajaxurl, {
            action: 'smartwriter_force_run_scheduler',
            nonce: smartwriterAjax.nonce
        }, function(response) {
            if (response.success) {
                alert('<?php _e('Scheduler run completed successfully!', 'smartwriter-ai'); ?>');
                location.reload();
            } else {
                alert('<?php _e('Scheduler run failed:', 'smartwriter-ai'); ?> ' + response.data);
            }
        }).always(function() {
            $button.text(originalText).prop('disabled', false);
        });
    });
});
</script>