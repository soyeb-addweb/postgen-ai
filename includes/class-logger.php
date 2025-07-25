<?php
/**
 * Logger Class
 * 
 * @package SmartWriter_AI
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

class SmartWriter_Logger {
    
    /**
     * Log levels
     */
    const LEVEL_ERROR = 'error';
    const LEVEL_WARNING = 'warning';
    const LEVEL_INFO = 'info';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_DEBUG = 'debug';
    
    /**
     * Maximum log entries to keep
     */
    const MAX_LOG_ENTRIES = 1000;
    
    /**
     * Log an entry
     */
    public function log($level, $message, $data = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        
        // Prepare log data
        $log_data = [
            'type' => sanitize_text_field($level),
            'message' => sanitize_text_field($message),
            'data' => $data ? json_encode($data) : null,
            'created_at' => current_time('mysql')
        ];
        
        // Insert log entry
        $result = $wpdb->insert($table_name, $log_data, ['%s', '%s', '%s', '%s']);
        
        if ($result === false) {
            // Fallback to error log if database insert fails
            error_log("SmartWriter AI Log [{$level}]: {$message}");
            if ($data) {
                error_log("SmartWriter AI Data: " . print_r($data, true));
            }
        }
        
        // Clean up old logs periodically
        if (rand(1, 100) === 1) { // 1% chance
            $this->cleanup_old_logs();
        }
        
        // Write to WordPress debug log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            $debug_message = "SmartWriter AI [{$level}]: {$message}";
            if ($data) {
                $debug_message .= " | Data: " . json_encode($data);
            }
            error_log($debug_message);
        }
    }
    
    /**
     * Log error
     */
    public function error($message, $data = null) {
        $this->log(self::LEVEL_ERROR, $message, $data);
    }
    
    /**
     * Log warning
     */
    public function warning($message, $data = null) {
        $this->log(self::LEVEL_WARNING, $message, $data);
    }
    
    /**
     * Log info
     */
    public function info($message, $data = null) {
        $this->log(self::LEVEL_INFO, $message, $data);
    }
    
    /**
     * Log success
     */
    public function success($message, $data = null) {
        $this->log(self::LEVEL_SUCCESS, $message, $data);
    }
    
    /**
     * Log debug
     */
    public function debug($message, $data = null) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $this->log(self::LEVEL_DEBUG, $message, $data);
        }
    }
    
    /**
     * Get logs
     */
    public function get_logs($limit = 50, $level = null, $offset = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        $where_clause = '';
        $params = [];
        
        if ($level && in_array($level, $this->get_log_levels())) {
            $where_clause = 'WHERE type = %s';
            $params[] = $level;
        }
        
        $query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        $logs = $wpdb->get_results($wpdb->prepare($query, $params));
        
        // Decode JSON data
        foreach ($logs as &$log) {
            if ($log->data) {
                $log->data = json_decode($log->data, true);
            }
        }
        
        return $logs;
    }
    
    /**
     * Get log statistics
     */
    public function get_log_stats($days = 7) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        $date_threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT type, COUNT(*) as count 
             FROM $table_name 
             WHERE created_at >= %s 
             GROUP BY type",
            $date_threshold
        ));
        
        $formatted_stats = [
            'total' => 0,
            'by_level' => []
        ];
        
        foreach ($stats as $stat) {
            $formatted_stats['by_level'][$stat->type] = (int) $stat->count;
            $formatted_stats['total'] += (int) $stat->count;
        }
        
        // Ensure all levels are represented
        foreach ($this->get_log_levels() as $level) {
            if (!isset($formatted_stats['by_level'][$level])) {
                $formatted_stats['by_level'][$level] = 0;
            }
        }
        
        return $formatted_stats;
    }
    
    /**
     * Get recent errors
     */
    public function get_recent_errors($limit = 10) {
        return $this->get_logs($limit, self::LEVEL_ERROR);
    }
    
    /**
     * Clear logs
     */
    public function clear_logs($level = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        
        if ($level && in_array($level, $this->get_log_levels())) {
            $result = $wpdb->delete($table_name, ['type' => $level], ['%s']);
        } else {
            $result = $wpdb->query("TRUNCATE TABLE $table_name");
        }
        
        if ($result !== false) {
            $this->log(self::LEVEL_INFO, 'Logs cleared', ['level' => $level ?: 'all']);
        }
        
        return $result !== false;
    }
    
    /**
     * Export logs
     */
    public function export_logs($format = 'json', $level = null, $limit = null) {
        $logs = $this->get_logs($limit ?: 1000, $level);
        
        switch ($format) {
            case 'csv':
                return $this->export_logs_csv($logs);
            case 'txt':
                return $this->export_logs_txt($logs);
            case 'json':
            default:
                return json_encode($logs, JSON_PRETTY_PRINT);
        }
    }
    
    /**
     * Export logs as CSV
     */
    private function export_logs_csv($logs) {
        $output = "ID,Type,Message,Data,Created At\n";
        
        foreach ($logs as $log) {
            $data = $log->data ? json_encode($log->data) : '';
            $output .= sprintf(
                '%d,"%s","%s","%s","%s"%s',
                $log->id,
                $log->type,
                str_replace('"', '""', $log->message),
                str_replace('"', '""', $data),
                $log->created_at,
                "\n"
            );
        }
        
        return $output;
    }
    
    /**
     * Export logs as TXT
     */
    private function export_logs_txt($logs) {
        $output = "SmartWriter AI - Log Export\n";
        $output .= "Generated: " . current_time('Y-m-d H:i:s') . "\n";
        $output .= str_repeat("=", 50) . "\n\n";
        
        foreach ($logs as $log) {
            $output .= sprintf(
                "[%s] %s: %s\n",
                $log->created_at,
                strtoupper($log->type),
                $log->message
            );
            
            if ($log->data) {
                $output .= "Data: " . json_encode($log->data, JSON_PRETTY_PRINT) . "\n";
            }
            
            $output .= str_repeat("-", 30) . "\n";
        }
        
        return $output;
    }
    
    /**
     * Get log levels
     */
    public function get_log_levels() {
        return [
            self::LEVEL_ERROR,
            self::LEVEL_WARNING,
            self::LEVEL_INFO,
            self::LEVEL_SUCCESS,
            self::LEVEL_DEBUG
        ];
    }
    
    /**
     * Get log level colors for display
     */
    public function get_log_level_colors() {
        return [
            self::LEVEL_ERROR => '#dc3545',
            self::LEVEL_WARNING => '#ffc107',
            self::LEVEL_INFO => '#17a2b8',
            self::LEVEL_SUCCESS => '#28a745',
            self::LEVEL_DEBUG => '#6c757d'
        ];
    }
    
    /**
     * Get log level icons
     */
    public function get_log_level_icons() {
        return [
            self::LEVEL_ERROR => 'dashicons-warning',
            self::LEVEL_WARNING => 'dashicons-flag',
            self::LEVEL_INFO => 'dashicons-info',
            self::LEVEL_SUCCESS => 'dashicons-yes-alt',
            self::LEVEL_DEBUG => 'dashicons-admin-tools'
        ];
    }
    
    /**
     * Cleanup old logs
     */
    private function cleanup_old_logs() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        
        // Count total logs
        $total_logs = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($total_logs > self::MAX_LOG_ENTRIES) {
            $logs_to_delete = $total_logs - self::MAX_LOG_ENTRIES;
            
            // Delete oldest logs
            $wpdb->query($wpdb->prepare(
                "DELETE FROM $table_name ORDER BY created_at ASC LIMIT %d",
                $logs_to_delete
            ));
        }
        
        // Also delete logs older than 30 days
        $cutoff_date = date('Y-m-d H:i:s', strtotime('-30 days'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < %s",
            $cutoff_date
        ));
    }
    
    /**
     * Get log entries count
     */
    public function get_log_count($level = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        
        if ($level && in_array($level, $this->get_log_levels())) {
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE type = %s",
                $level
            ));
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }
    
    /**
     * Search logs
     */
    public function search_logs($search_term, $limit = 50, $level = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        $where_conditions = ["message LIKE %s"];
        $params = ['%' . $wpdb->esc_like($search_term) . '%'];
        
        if ($level && in_array($level, $this->get_log_levels())) {
            $where_conditions[] = "type = %s";
            $params[] = $level;
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        $query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d";
        $params[] = $limit;
        
        $logs = $wpdb->get_results($wpdb->prepare($query, $params));
        
        // Decode JSON data
        foreach ($logs as &$log) {
            if ($log->data) {
                $log->data = json_decode($log->data, true);
            }
        }
        
        return $logs;
    }
    
    /**
     * Get logs by date range
     */
    public function get_logs_by_date_range($start_date, $end_date, $limit = 100, $level = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartwriter_logs';
        $where_conditions = ["created_at BETWEEN %s AND %s"];
        $params = [$start_date, $end_date];
        
        if ($level && in_array($level, $this->get_log_levels())) {
            $where_conditions[] = "type = %s";
            $params[] = $level;
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        $query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d";
        $params[] = $limit;
        
        $logs = $wpdb->get_results($wpdb->prepare($query, $params));
        
        // Decode JSON data
        foreach ($logs as &$log) {
            if ($log->data) {
                $log->data = json_decode($log->data, true);
            }
        }
        
        return $logs;
    }
    
    /**
     * Check system health based on logs
     */
    public function get_system_health() {
        $recent_errors = $this->get_logs(10, self::LEVEL_ERROR);
        $stats = $this->get_log_stats(24); // Last 24 hours
        
        $health_score = 100;
        $issues = [];
        
        // Check error rate
        $error_rate = $stats['total'] > 0 ? ($stats['by_level'][self::LEVEL_ERROR] / $stats['total']) * 100 : 0;
        
        if ($error_rate > 50) {
            $health_score -= 40;
            $issues[] = 'High error rate detected';
        } elseif ($error_rate > 25) {
            $health_score -= 20;
            $issues[] = 'Moderate error rate detected';
        }
        
        // Check for recent critical errors
        foreach ($recent_errors as $error) {
            if (strpos(strtolower($error->message), 'api') !== false) {
                $health_score -= 15;
                $issues[] = 'API connection issues detected';
                break;
            }
        }
        
        // Check for database issues
        foreach ($recent_errors as $error) {
            if (strpos(strtolower($error->message), 'database') !== false || 
                strpos(strtolower($error->message), 'mysql') !== false) {
                $health_score -= 20;
                $issues[] = 'Database issues detected';
                break;
            }
        }
        
        $health_score = max(0, $health_score);
        
        return [
            'score' => $health_score,
            'status' => $this->get_health_status($health_score),
            'issues' => $issues,
            'last_check' => current_time('Y-m-d H:i:s'),
            'error_rate' => round($error_rate, 2),
            'recent_errors_count' => count($recent_errors)
        ];
    }
    
    /**
     * Get health status text
     */
    private function get_health_status($score) {
        if ($score >= 80) {
            return 'Excellent';
        } elseif ($score >= 60) {
            return 'Good';
        } elseif ($score >= 40) {
            return 'Fair';
        } elseif ($score >= 20) {
            return 'Poor';
        } else {
            return 'Critical';
        }
    }
    
    /**
     * Get dashboard data
     */
    public function get_dashboard_data() {
        $stats = $this->get_log_stats(7);
        $health = $this->get_system_health();
        $recent_logs = $this->get_logs(5);
        
        return [
            'stats' => $stats,
            'health' => $health,
            'recent_logs' => $recent_logs,
            'total_logs' => $this->get_log_count()
        ];
    }
}