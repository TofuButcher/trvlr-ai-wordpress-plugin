<?php

/**
 * Main Settings Page with Tab System
 * 
 * @package Trvlr
 */

// Prevent direct access
if (! defined('ABSPATH')) exit;

// Define tabs - Add new tabs here easily
$trvlr_tabs = array(
   'setup' => array(
      'label' => __('Setup', 'trvlr'),
      'file'  => 'trvlr-settings-setup.php',
      'icon'  => 'dashicons-admin-settings'
   ),
   'theme' => array(
      'label' => __('Theme', 'trvlr'),
      'file'  => 'trvlr-settings-theme.php',
      'icon'  => 'dashicons-admin-appearance'
   ),
   'sync' => array(
      'label' => __('Sync', 'trvlr'),
      'file'  => 'trvlr-settings-sync.php',
      'icon'  => 'dashicons-update'
   ),
   'logs' => array(
      'label' => __('Logs', 'trvlr'),
      'file'  => 'trvlr-settings-logs.php',
      'icon'  => 'dashicons-list-view'
   ),
);

// Allow other plugins/themes to add tabs via filter
$trvlr_tabs = apply_filters('trvlr_settings_tabs', $trvlr_tabs);

// Get current tab (default to first)
$current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : array_key_first($trvlr_tabs);

// Validate current tab exists
if (! isset($trvlr_tabs[$current_tab])) {
   $current_tab = array_key_first($trvlr_tabs);
}

?>

<?php include_once plugin_dir_path(__FILE__) . 'trvlr-admin-header.php'; ?>

<div class="trvlr-settings-wrapper">

   <!-- Tab Navigation -->
   <nav class="trvlr-tabs-nav">
      <?php foreach ($trvlr_tabs as $tab_key => $tab_data) : ?>
         <a href="#"
            class="trvlr-tab-link <?php echo $current_tab === $tab_key ? 'active' : ''; ?>"
            data-tab="<?php echo esc_attr($tab_key); ?>">
            <span class="dashicons <?php echo esc_attr($tab_data['icon']); ?>"></span>
            <?php echo esc_html($tab_data['label']); ?>
         </a>
      <?php endforeach; ?>
   </nav>

   <!-- Tab Content Container -->
   <div class="trvlr-tabs-content">
      <?php foreach ($trvlr_tabs as $tab_key => $tab_data) : ?>
         <div class="trvlr-tab-pane <?php echo $current_tab === $tab_key ? 'active' : ''; ?>"
            id="trvlr-tab-<?php echo esc_attr($tab_key); ?>"
            data-tab="<?php echo esc_attr($tab_key); ?>">
            <?php
            // Include tab content file
            $tab_file = plugin_dir_path(__FILE__) . $tab_data['file'];
            if (file_exists($tab_file)) {
               include $tab_file;
            } else {
               echo '<div class="notice notice-warning"><p>';
               printf(
                  /* translators: %s: tab file name */
                  esc_html__('Tab content file not found: %s', 'trvlr'),
                  esc_html($tab_data['file'])
               );
               echo '</p></div>';
            }
            ?>
         </div>
      <?php endforeach; ?>
   </div>

</div>

<?php include_once plugin_dir_path(__FILE__) . 'trvlr-admin-footer.php'; ?>