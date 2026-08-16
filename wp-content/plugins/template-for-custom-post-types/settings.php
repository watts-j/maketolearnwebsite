<?php
$tcp_options = get_option('wptcp_options');
$tcp_pts = isset($tcp_options['objects']) ? $tcp_options['objects'] : array();
?>

<div class="wrap">
    <?php screen_icon('plugins'); ?>
    <h2><?php _e('Templates for Custom Post Types Settings', WPTCP_PLUGIN_NAME); ?></h2>
    <?php if (isset($_GET['msg'])) : ?>
        <div id="message" class="updated below-h2">
            <?php if ($_GET['msg'] == 'update') : ?>
                <p><?php _e('Settings Updated.',WPTCP_PLUGIN_NAME); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <?php if (function_exists('wp_nonce_field')) wp_nonce_field('nonce_wptcp'); ?>

        <div id="tcp_select_objects">

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <p><?php _e('Check to Apply Templates for Custom Post Types', WPTCP_PLUGIN_NAME) ?></p>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            $post_types = get_post_types(array(                                
                                'public'   => true,
                                    ), 'objects');

                            foreach ($post_types as $post_type) {
                                if ($post_type->name == 'attachment')
                                    continue;
                                ?>
                                <label><input type="checkbox" name="objects[]" value="<?php echo $post_type->name; ?>" <?php
                                    if (isset($tcp_pts) && is_array($tcp_pts)) {
                                        if (in_array($post_type->name, $tcp_pts)) {
                                            echo 'checked="checked"';
                                        }
                                    }
                                    ?>>&nbsp;<?php echo $post_type->label; ?></label><br>
                                    <?php
                                }
                                ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        
        <p class="submit">
            <input type="submit" class="button-primary" name="wptcp_submit" value="<?php _e('Save', WPTCP_PLUGIN_NAME); ?>">
        </p>

    </form>
</div>