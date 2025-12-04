<?php

/**
 * Setup Tab - Core Settings
 * 
 * @package Trvlr
 */

if (! defined('ABSPATH')) exit;
?>

<div class="trvlr-tab-content">
    <h2><?php esc_html_e('Setup', 'trvlr'); ?></h2>
    <p class="description"><?php esc_html_e('Configure your TRVLR AI connection settings.', 'trvlr'); ?></p>

    <?php include_once plugin_dir_path(__FILE__) . 'trvlr-settings-setup-status.php'; ?>

    <form method="post" action="options.php" class="trvlr-settings-form">
        <?php
        settings_fields('trvlr_settings_group');
        do_settings_sections('trvlr_settings_group');
        ?>

        <table class="form-table" role="presentation">
            <tr valign="top">
                <th scope="row">
                    <label for="trvlr_organisation_id"><?php esc_html_e('Organisation ID', 'trvlr'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="trvlr_organisation_id"
                        name="trvlr_organisation_id"
                        value="<?php echo esc_attr(get_option('trvlr_organisation_id')); ?>"
                        class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Your Organisation ID from TRVLR AI.', 'trvlr'); ?>
                    </p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="trvlr_api_key"><?php esc_html_e('API Key', 'trvlr'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="trvlr_api_key"
                        name="trvlr_api_key"
                        value="<?php echo esc_attr(get_option('trvlr_api_key')); ?>"
                        class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('API Key for authentication (if required).', 'trvlr'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <?php include_once plugin_dir_path(__FILE__) . 'trvlr-settings-setup-instructions.php'; ?>

    <hr>

    <?php include_once plugin_dir_path(__FILE__) . 'trvlr-settings-notifications.php'; ?>

</div>