<?php
/**
 * Admin Scheduler Template
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Get instances
$scheduler = new SmartWriter_Scheduler();
$logger = new SmartWriter_Logger();

// Get scheduled posts
$scheduled_posts = $scheduler->get_scheduled_posts(50);
$scheduler_status = $scheduler->get_scheduler_status();

// Handle form submissions
if (isset($_POST['action']) && check_admin_referer('smartwriter_scheduler_nonce')) {
    switch ($_POST['action']) {
        case 'schedule_single':
            $topic = sanitize_text_field($_POST['topic']);
            $schedule_date = sanitize_text_field($_POST['schedule_date']);
            if ($topic && $schedule_date) {
                $scheduler->add_scheduled_post($topic, $schedule_date);
                echo '<div class="notice notice-success"><p>Post scheduled successfully!</p></div>';
            }
            break;
            
        case 'schedule_bulk':
            $topics = array_map('sanitize_text_field', explode("\n", $_POST['bulk_topics']));
            $start_date = sanitize_text_field($_POST['bulk_start_date']);
            $end_date = sanitize_text_field($_POST['bulk_end_date']);
            
            if (!empty($topics) && $start_date && $end_date) {
                $scheduler->schedule_backdate_posts($start_date, $end_date);
                echo '<div class="notice notice-success"><p>Bulk posts scheduled successfully!</p></div>';
            }
            break;
    }
}
?>

<div class="wrap smartwriter-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-calendar"></span>
        <?php esc_html_e('Schedule Posts', 'smartwriter-ai'); ?>
    </h1>

    <!-- Scheduler Status -->
    <div class="smartwriter-card" style="margin-top: 20px;">
        <h2>
            <span class="dashicons dashicons-info"></span>
            <?php _e('Scheduler Status', 'smartwriter-ai'); ?>
        </h2>
        
        <div class="scheduler-status-overview" style="padding: 20px;">
            <div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="status-item">
                    <h4><?php _e('Status', 'smartwriter-ai'); ?></h4>
                    <span class="status-indicator <?php echo $scheduler_status['active'] ? 'active' : 'inactive'; ?>">
                        <span class="indicator-dot"></span>
                        <?php echo $scheduler_status['active'] ? __('Active', 'smartwriter-ai') : __('Inactive', 'smartwriter-ai'); ?>
                    </span>
                </div>
                
                <div class="status-item">
                    <h4><?php _e('Next Run', 'smartwriter-ai'); ?></h4>
                    <p><?php echo $scheduler_status['next_run'] ? date('M j, Y g:i A', strtotime($scheduler_status['next_run'])) : __('Not scheduled', 'smartwriter-ai'); ?></p>
                </div>
                
                <div class="status-item">
                    <h4><?php _e('Posts Today', 'smartwriter-ai'); ?></h4>
                    <p><?php echo $scheduler_status['posts_today']; ?>/<?php echo $scheduler_status['max_posts_per_day']; ?></p>
                </div>
                
                <div class="status-item">
                    <h4><?php _e('Posting Window', 'smartwriter-ai'); ?></h4>
                    <p><?php echo $scheduler_status['posting_window']['start']; ?> - <?php echo $scheduler_status['posting_window']['end']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="smartwriter-dashboard-grid">
        <!-- Left Column: Scheduling Forms -->
        <div class="smartwriter-dashboard-left">
            
            <!-- Schedule Single Post -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e('Schedule Single Post', 'smartwriter-ai'); ?>
                </h2>
                
                <form method="post" style="padding: 20px;">
                    <?php wp_nonce_field('smartwriter_scheduler_nonce'); ?>
                    <input type="hidden" name="action" value="schedule_single">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="topic"><?php _e('Topic', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="topic" name="topic" class="regular-text" placeholder="<?php _e('Enter topic for the post...', 'smartwriter-ai'); ?>" required />
                                <p class="description"><?php _e('Enter the topic or subject for the AI to write about.', 'smartwriter-ai'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="schedule_date"><?php _e('Schedule Date & Time', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <input type="datetime-local" id="schedule_date" name="schedule_date" class="regular-text" required />
                                <p class="description"><?php _e('When should this post be generated and published?', 'smartwriter-ai'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php _e('Schedule Post', 'smartwriter-ai'); ?>" />
                    </p>
                </form>
            </div>

            <!-- Bulk Schedule -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Bulk Schedule Posts', 'smartwriter-ai'); ?>
                </h2>
                
                <form method="post" style="padding: 20px;">
                    <?php wp_nonce_field('smartwriter_scheduler_nonce'); ?>
                    <input type="hidden" name="action" value="schedule_bulk">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="bulk_topics"><?php _e('Topics', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <textarea id="bulk_topics" name="bulk_topics" rows="6" class="large-text" placeholder="<?php _e('Enter topics, one per line...', 'smartwriter-ai'); ?>"></textarea>
                                <p class="description"><?php _e('Enter multiple topics, one per line. Each will be scheduled automatically.', 'smartwriter-ai'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="bulk_start_date"><?php _e('Date Range', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="bulk_start_date" name="bulk_start_date" /> to 
                                <input type="date" id="bulk_end_date" name="bulk_end_date" />
                                <p class="description"><?php _e('Posts will be distributed evenly across this date range.', 'smartwriter-ai'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php _e('Schedule Bulk Posts', 'smartwriter-ai'); ?>" />
                    </p>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-controls-play"></span>
                    <?php _e('Quick Actions', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="quick-actions" style="padding: 20px;">
                    <button type="button" id="force-run-now" class="button button-secondary">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Run Now', 'smartwriter-ai'); ?>
                    </button>
                    
                    <button type="button" id="clear-completed" class="button button-secondary">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e('Clear Completed', 'smartwriter-ai'); ?>
                    </button>
                    
                    <button type="button" id="export-schedule" class="button button-secondary">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export Schedule', 'smartwriter-ai'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Scheduled Posts List -->
        <div class="smartwriter-dashboard-right">
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <?php _e('Scheduled Posts', 'smartwriter-ai'); ?>
                    <span class="count">(<?php echo count($scheduled_posts); ?>)</span>
                </h2>
                
                <div class="scheduled-posts-filters" style="padding: 15px 20px; border-bottom: 1px solid #e5e5e5;">
                    <select id="status-filter">
                        <option value=""><?php _e('All Statuses', 'smartwriter-ai'); ?></option>
                        <option value="pending"><?php _e('Pending', 'smartwriter-ai'); ?></option>
                        <option value="processing"><?php _e('Processing', 'smartwriter-ai'); ?></option>
                        <option value="completed"><?php _e('Completed', 'smartwriter-ai'); ?></option>
                        <option value="failed"><?php _e('Failed', 'smartwriter-ai'); ?></option>
                    </select>
                    
                    <input type="text" id="search-posts" placeholder="<?php _e('Search posts...', 'smartwriter-ai'); ?>" />
                </div>
                
                <?php if (!empty($scheduled_posts)): ?>
                    <div class="scheduled-posts-list" style="max-height: 500px; overflow-y: auto;">
                        <?php foreach ($scheduled_posts as $post): ?>
                            <div class="scheduled-post-item" data-status="<?php echo esc_attr($post->status); ?>" style="padding: 15px 20px; border-bottom: 1px solid #f0f0f1;">
                                <div class="post-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div class="post-info" style="flex: 1;">
                                        <h4 style="margin: 0 0 5px 0;"><?php echo esc_html(wp_trim_words($post->prompt, 8)); ?></h4>
                                        <div class="post-meta" style="font-size: 12px; color: #646970;">
                                            <span><?php _e('Scheduled:', 'smartwriter-ai'); ?> <?php echo date('M j, Y g:i A', strtotime($post->schedule_date)); ?></span>
                                            <?php if ($post->processed_at): ?>
                                                <span> • <?php _e('Processed:', 'smartwriter-ai'); ?> <?php echo date('M j, Y g:i A', strtotime($post->processed_at)); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="post-status">
                                        <span class="status-badge status-<?php echo $post->status; ?>" style="
                                            padding: 2px 8px; 
                                            border-radius: 12px; 
                                            font-size: 11px; 
                                            font-weight: 500; 
                                            text-transform: uppercase;
                                            <?php 
                                            switch($post->status) {
                                                case 'pending': echo 'background: #fff3e0; color: #f57c00;'; break;
                                                case 'processing': echo 'background: #e3f2fd; color: #1976d2;'; break;
                                                case 'completed': echo 'background: #e8f5e8; color: #2e7d32;'; break;
                                                case 'failed': echo 'background: #ffebee; color: #c62828;'; break;
                                            }
                                            ?>
                                        ">
                                            <?php echo ucfirst($post->status); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php if ($post->post_id): ?>
                                    <div class="post-link" style="margin-top: 8px;">
                                        <a href="<?php echo get_edit_post_link($post->post_id); ?>" target="_blank" class="button button-small">
                                            <?php _e('View Post', 'smartwriter-ai'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($post->error_message): ?>
                                    <div class="error-message" style="margin-top: 8px; padding: 8px; background: #ffebee; border-radius: 4px; color: #c62828; font-size: 12px;">
                                        <?php echo esc_html($post->error_message); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-actions" style="margin-top: 10px;">
                                    <?php if ($post->status === 'pending'): ?>
                                        <button type="button" class="button button-small edit-scheduled-post" data-id="<?php echo $post->id; ?>">
                                            <?php _e('Edit', 'smartwriter-ai'); ?>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($post->status === 'failed'): ?>
                                        <button type="button" class="button button-small retry-post" data-id="<?php echo $post->id; ?>">
                                            <?php _e('Retry', 'smartwriter-ai'); ?>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button type="button" class="button button-small delete-scheduled-post" data-id="<?php echo $post->id; ?>">
                                        <?php _e('Delete', 'smartwriter-ai'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-scheduled-posts" style="padding: 40px 20px; text-align: center; color: #646970;">
                        <span class="dashicons dashicons-calendar-alt" style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></span>
                        <p><?php _e('No posts scheduled yet.', 'smartwriter-ai'); ?></p>
                        <p><?php _e('Use the forms on the left to schedule your first post!', 'smartwriter-ai'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Force run scheduler
    $('#force-run-now').on('click', function() {
        if (!confirm('<?php _e('Force run the scheduler now?', 'smartwriter-ai'); ?>')) {
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
                alert('<?php _e('Scheduler run completed!', 'smartwriter-ai'); ?>');
                location.reload();
            } else {
                alert('<?php _e('Error:', 'smartwriter-ai'); ?> ' + response.data);
            }
        }).always(function() {
            $button.text(originalText).prop('disabled', false);
        });
    });
    
    // Filter scheduled posts
    $('#status-filter').on('change', function() {
        var status = $(this).val();
        $('.scheduled-post-item').each(function() {
            if (status === '' || $(this).data('status') === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Search scheduled posts
    $('#search-posts').on('input', function() {
        var search = $(this).val().toLowerCase();
        $('.scheduled-post-item').each(function() {
            var text = $(this).text().toLowerCase();
            if (text.indexOf(search) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Delete scheduled post
    $('.delete-scheduled-post').on('click', function() {
        if (!confirm('<?php _e('Delete this scheduled post?', 'smartwriter-ai'); ?>')) {
            return;
        }
        
        var $button = $(this);
        var postId = $button.data('id');
        
        $.post(ajaxurl, {
            action: 'smartwriter_delete_scheduled_post',
            nonce: smartwriterAjax.nonce,
            post_id: postId
        }, function(response) {
            if (response.success) {
                $button.closest('.scheduled-post-item').fadeOut();
            } else {
                alert('<?php _e('Error deleting post:', 'smartwriter-ai'); ?> ' + response.data);
            }
        });
    });
    
    // Set default datetime for new post
    var now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('#schedule_date').val(now.toISOString().slice(0, 16));
    
    // Set default dates for bulk scheduling
    var today = new Date().toISOString().slice(0, 10);
    var nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10);
    $('#bulk_start_date').val(today);
    $('#bulk_end_date').val(nextWeek);
});
</script>