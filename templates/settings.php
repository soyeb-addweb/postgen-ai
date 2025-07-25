<?php
defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?php esc_html_e('PostGen AI Settings', 'postgen-ai'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('postgen_ai_settings_group');
        do_settings_sections('postgen-ai');
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('API Key', 'postgen-ai'); ?></th>
                <td><input type="text" name="postgen_ai_api_key" value="<?php echo esc_attr(get_option('postgen_ai_api_key')); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Prompt Template', 'postgen-ai'); ?></th>
                <td><textarea name="postgen_ai_prompt_template" class="large-text" rows="5"><?php echo esc_textarea(get_option('postgen_ai_prompt_template')); ?></textarea></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>