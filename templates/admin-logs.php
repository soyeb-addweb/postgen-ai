<?php
/**
 * Admin Logs Template
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Get instances
$logger = new SmartWriter_Logger();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';

if (isset($_POST['action']) && check_admin_referer('smartwriter_logs_nonce')) {
    switch ($_POST['action']) {
        case 'clear_logs':
            $count = $logger->clear_logs();
            $message = sprintf(__('Cleared %d log entries.', 'smartwriter-ai'), $count);
            break;
            
        case 'export_logs':
            $filename = $logger->export_logs();
            if ($filename) {
                $message = sprintf(__('Logs exported to: %s', 'smartwriter-ai'), $filename);
            } else {
                $message = __('Failed to export logs.', 'smartwriter-ai');
            }
            break;
    }
}

// Get logs with pagination
$page = max(1, intval($_GET['log_page'] ?? 1));
$per_page = 50;
$level_filter = $_GET['level'] ?? '';
$search_query = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$logs = $logger->get_logs($per_page, $page, $level_filter, $search_query, $date_from, $date_to);
$total_logs = $logger->get_log_count($level_filter, $search_query, $date_from, $date_to);
$total_pages = ceil($total_logs / $per_page);

// Get statistics
$stats = $logger->get_log_stats();
$recent_errors = $logger->get_recent_errors(5);
$system_health = $logger->get_system_health();
?>

<div class="wrap smartwriter-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-list-view"></span>
        <?php esc_html_e('Activity Logs', 'smartwriter-ai'); ?>
    </h1>

    <?php if ($message): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <!-- Log Statistics Overview -->
    <div class="smartwriter-card" style="margin-top: 20px;">
        <h2>
            <span class="dashicons dashicons-chart-bar"></span>
            <?php _e('Log Statistics', 'smartwriter-ai'); ?>
        </h2>
        
        <div class="log-stats-overview" style="padding: 20px;">
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                <?php foreach ($stats as $level => $count): ?>
                    <div class="stat-item" style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <div class="stat-count" style="font-size: 24px; font-weight: bold; color: <?php echo $logger->get_log_level_colors()[$level] ?? '#333'; ?>;">
                            <?php echo number_format($count); ?>
                        </div>
                        <div class="stat-label" style="font-size: 12px; text-transform: uppercase; color: #666; margin-top: 5px;">
                            <?php echo ucfirst($level); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="stat-item" style="text-align: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div class="stat-count" style="font-size: 24px; font-weight: bold; color: #333;">
                        <?php echo number_format($total_logs); ?>
                    </div>
                    <div class="stat-label" style="font-size: 12px; text-transform: uppercase; color: #666; margin-top: 5px;">
                        Total
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="smartwriter-dashboard-grid">
        <!-- Left Column: Filters and Actions -->
        <div class="smartwriter-dashboard-left">
            
            <!-- Log Filters -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-filter"></span>
                    <?php _e('Filter Logs', 'smartwriter-ai'); ?>
                </h2>
                
                <form method="get" style="padding: 20px;">
                    <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>" />
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="level"><?php _e('Log Level', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <select name="level" id="level">
                                    <option value=""><?php _e('All Levels', 'smartwriter-ai'); ?></option>
                                    <?php foreach ($logger->get_log_levels() as $level): ?>
                                        <option value="<?php echo $level; ?>" <?php selected($level_filter, $level); ?>>
                                            <?php echo ucfirst($level); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="search"><?php _e('Search', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="search" id="search" value="<?php echo esc_attr($search_query); ?>" class="regular-text" placeholder="<?php _e('Search log messages...', 'smartwriter-ai'); ?>" />
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="date_from"><?php _e('Date Range', 'smartwriter-ai'); ?></label>
                            </th>
                            <td>
                                <input type="date" name="date_from" id="date_from" value="<?php echo esc_attr($date_from); ?>" />
                                to
                                <input type="date" name="date_to" id="date_to" value="<?php echo esc_attr($date_to); ?>" />
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="<?php _e('Filter Logs', 'smartwriter-ai'); ?>" />
                        <a href="<?php echo admin_url('admin.php?page=smartwriter-ai-logs'); ?>" class="button button-secondary">
                            <?php _e('Clear Filters', 'smartwriter-ai'); ?>
                        </a>
                    </p>
                </form>
            </div>

            <!-- System Health -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-heart"></span>
                    <?php _e('System Health', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="system-health" style="padding: 20px;">
                    <div class="health-score" style="text-align: center; margin-bottom: 20px;">
                        <div class="health-circle" style="
                            width: 100px; 
                            height: 100px; 
                            border-radius: 50%; 
                            margin: 0 auto 10px; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center; 
                            font-size: 24px; 
                            font-weight: bold; 
                            color: white;
                            background: <?php echo $system_health['score'] >= 80 ? '#4caf50' : ($system_health['score'] >= 60 ? '#ff9800' : '#f44336'); ?>;
                        ">
                            <?php echo $system_health['score']; ?>%
                        </div>
                        <div class="health-status" style="font-weight: bold; color: <?php echo $system_health['score'] >= 80 ? '#4caf50' : ($system_health['score'] >= 60 ? '#ff9800' : '#f44336'); ?>;">
                            <?php echo $system_health['status']; ?>
                        </div>
                    </div>
                    
                    <div class="health-details">
                        <ul style="list-style: none; padding: 0;">
                            <?php foreach ($system_health['details'] as $detail): ?>
                                <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                                    <span class="dashicons dashicons-<?php echo $detail['icon']; ?>" style="color: <?php echo $detail['color']; ?>;"></span>
                                    <?php echo esc_html($detail['message']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            <?php if (!empty($recent_errors)): ?>
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-warning"></span>
                    <?php _e('Recent Errors', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="recent-errors" style="padding: 20px;">
                    <?php foreach ($recent_errors as $error): ?>
                        <div class="error-item" style="margin-bottom: 15px; padding: 10px; background: #ffebee; border-left: 4px solid #f44336; border-radius: 4px;">
                            <div class="error-time" style="font-size: 11px; color: #666; margin-bottom: 5px;">
                                <?php echo date('M j, Y g:i A', strtotime($error->created_at)); ?>
                            </div>
                            <div class="error-message" style="font-size: 13px;">
                                <?php echo esc_html(wp_trim_words($error->message, 15)); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Log Actions -->
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Log Management', 'smartwriter-ai'); ?>
                </h2>
                
                <div class="log-actions" style="padding: 20px;">
                    <form method="post" style="margin-bottom: 15px;">
                        <?php wp_nonce_field('smartwriter_logs_nonce'); ?>
                        <input type="hidden" name="action" value="export_logs">
                        <button type="submit" class="button button-secondary">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e('Export Logs', 'smartwriter-ai'); ?>
                        </button>
                    </form>
                    
                    <form method="post" onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to clear all logs? This action cannot be undone.', 'smartwriter-ai'); ?>');">
                        <?php wp_nonce_field('smartwriter_logs_nonce'); ?>
                        <input type="hidden" name="action" value="clear_logs">
                        <button type="submit" class="button button-secondary">
                            <span class="dashicons dashicons-trash"></span>
                            <?php _e('Clear All Logs', 'smartwriter-ai'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Log Entries -->
        <div class="smartwriter-dashboard-right">
            <div class="smartwriter-card">
                <h2>
                    <span class="dashicons dashicons-editor-ul"></span>
                    <?php _e('Log Entries', 'smartwriter-ai'); ?>
                    <span class="count">(<?php echo number_format($total_logs); ?>)</span>
                </h2>
                
                <?php if (!empty($logs)): ?>
                    <!-- Pagination Top -->
                    <div class="log-pagination" style="padding: 15px 20px; border-bottom: 1px solid #e5e5e5;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="showing-info">
                                <?php
                                $start = ($page - 1) * $per_page + 1;
                                $end = min($page * $per_page, $total_logs);
                                printf(__('Showing %d-%d of %d entries', 'smartwriter-ai'), $start, $end, $total_logs);
                                ?>
                            </div>
                            
                            <div class="pagination-links">
                                <?php if ($page > 1): ?>
                                    <a href="<?php echo add_query_arg('log_page', $page - 1); ?>" class="button">&laquo; <?php _e('Previous', 'smartwriter-ai'); ?></a>
                                <?php endif; ?>
                                
                                <span class="current-page"><?php printf(__('Page %d of %d', 'smartwriter-ai'), $page, $total_pages); ?></span>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="<?php echo add_query_arg('log_page', $page + 1); ?>" class="button"><?php _e('Next', 'smartwriter-ai'); ?> &raquo;</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Log Entries List -->
                    <div class="log-entries-list" style="max-height: 600px; overflow-y: auto;">
                        <?php foreach ($logs as $log): ?>
                            <div class="log-entry log-level-<?php echo $log->level; ?>" style="
                                padding: 15px 20px; 
                                border-bottom: 1px solid #f0f0f1;
                                border-left: 4px solid <?php echo $logger->get_log_level_colors()[$log->level] ?? '#ccc'; ?>;
                            ">
                                <div class="log-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div class="log-level-info">
                                        <span class="log-level-badge" style="
                                            display: inline-block;
                                            padding: 2px 8px;
                                            border-radius: 12px;
                                            font-size: 11px;
                                            font-weight: 500;
                                            text-transform: uppercase;
                                            background: <?php echo $logger->get_log_level_colors()[$log->level] ?? '#ccc'; ?>;
                                            color: white;
                                        ">
                                            <span class="dashicons <?php echo $logger->get_log_level_icons()[$log->level] ?? 'dashicons-info'; ?>" style="font-size: 12px; line-height: 1;"></span>
                                            <?php echo ucfirst($log->level); ?>
                                        </span>
                                        
                                        <?php if ($log->component): ?>
                                            <span class="log-component" style="
                                                margin-left: 8px;
                                                padding: 2px 6px;
                                                background: #f0f0f1;
                                                border-radius: 4px;
                                                font-size: 11px;
                                                color: #646970;
                                            ">
                                                <?php echo esc_html($log->component); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="log-time" style="font-size: 12px; color: #646970;">
                                        <?php echo date('M j, Y g:i:s A', strtotime($log->created_at)); ?>
                                    </div>
                                </div>
                                
                                <div class="log-message" style="margin-bottom: 8px; line-height: 1.5;">
                                    <?php echo nl2br(esc_html($log->message)); ?>
                                </div>
                                
                                <?php if ($log->context): ?>
                                    <div class="log-context">
                                        <button type="button" class="toggle-context button button-small" style="margin-bottom: 8px;">
                                            <?php _e('Show Context', 'smartwriter-ai'); ?>
                                        </button>
                                        <div class="context-data" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; overflow-x: auto;">
                                            <pre style="margin: 0; white-space: pre-wrap;"><?php echo esc_html(json_encode(json_decode($log->context), JSON_PRETTY_PRINT)); ?></pre>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination Bottom -->
                    <div class="log-pagination" style="padding: 15px 20px; border-top: 1px solid #e5e5e5;">
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo add_query_arg('log_page', 1); ?>" class="button">&laquo;&laquo; <?php _e('First', 'smartwriter-ai'); ?></a>
                                <a href="<?php echo add_query_arg('log_page', $page - 1); ?>" class="button">&laquo; <?php _e('Previous', 'smartwriter-ai'); ?></a>
                            <?php endif; ?>
                            
                            <span class="current-page" style="margin: 0 15px;"><?php printf(__('Page %d of %d', 'smartwriter-ai'), $page, $total_pages); ?></span>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="<?php echo add_query_arg('log_page', $page + 1); ?>" class="button"><?php _e('Next', 'smartwriter-ai'); ?> &raquo;</a>
                                <a href="<?php echo add_query_arg('log_page', $total_pages); ?>" class="button"><?php _e('Last', 'smartwriter-ai'); ?> &raquo;&raquo;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="no-logs" style="padding: 40px 20px; text-align: center; color: #646970;">
                        <span class="dashicons dashicons-admin-page" style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></span>
                        <p><?php _e('No log entries found.', 'smartwriter-ai'); ?></p>
                        <?php if ($level_filter || $search_query || $date_from || $date_to): ?>
                            <p><?php _e('Try adjusting your filters or clearing them to see more results.', 'smartwriter-ai'); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle context data
    $('.toggle-context').on('click', function() {
        var $button = $(this);
        var $context = $button.siblings('.context-data');
        
        if ($context.is(':visible')) {
            $context.slideUp();
            $button.text('<?php _e('Show Context', 'smartwriter-ai'); ?>');
        } else {
            $context.slideDown();
            $button.text('<?php _e('Hide Context', 'smartwriter-ai'); ?>');
        }
    });
    
    // Auto-refresh logs every 30 seconds if on first page with no filters
    <?php if ($page === 1 && !$level_filter && !$search_query && !$date_from && !$date_to): ?>
    setInterval(function() {
        location.reload();
    }, 30000);
    <?php endif; ?>
    
    // Real-time log level statistics
    function updateLogStats() {
        $.post(ajaxurl, {
            action: 'smartwriter_get_log_stats',
            nonce: smartwriterAjax.nonce
        }, function(response) {
            if (response.success) {
                // Update stats display
                $('.stats-grid .stat-count').each(function(index, element) {
                    var level = $(element).siblings('.stat-label').text().toLowerCase();
                    if (response.data[level] !== undefined) {
                        $(element).text(response.data[level].toLocaleString());
                    }
                });
            }
        });
    }
    
    // Update stats every minute
    setInterval(updateLogStats, 60000);
});
</script>