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

    <hr style="margin: 20px 0;">

    <!-- React Setup Settings Component -->
    <div id="trvlr-setup-settings-root"></div>

    <hr style="margin: 30px 0;">

    <?php include_once plugin_dir_path(__FILE__) . 'trvlr-settings-setup-instructions.php'; ?>

</div>