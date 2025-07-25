<?php
defined('ABSPATH') || exit;

class PostGen_Logger {
    public static function log($message) {
        $log_file = POSTGEN_AI_DIR . 'log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    }
}
