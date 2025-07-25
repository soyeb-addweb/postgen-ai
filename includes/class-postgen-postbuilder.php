<?php
defined('ABSPATH') || exit;

class PostGen_PostBuilder {
    public function create_post_from_response($data) {
        $post_data = [
            'post_title' => sanitize_text_field($data['title'] ?? 'Untitled'),
            'post_content' => wp_kses_post($data['content'] ?? ''),
            'post_status' => 'publish',
            'post_type' => 'post'
        ];

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            // Add SEO meta if available
            if (!empty($data['seo_title'])) {
                update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($data['seo_title']));
            }
            if (!empty($data['seo_description'])) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_text_field($data['seo_description']));
            }
        }
    }
}
