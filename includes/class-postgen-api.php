<?php
defined('ABSPATH') || exit;

class PostGen_API {
    public function fetch_content($prompt) {
        $api_key = get_option('postgen_ai_api_key');
        if (empty($api_key)) return false;

        // This should be replaced with Perplexity API logic
        $response = wp_remote_post('https://api.perplexity.ai/v1/query', [
            'headers' => ['Authorization' => 'Bearer ' . $api_key],
            'body' => json_encode(['prompt' => $prompt]),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) return false;

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body;
    }
}
