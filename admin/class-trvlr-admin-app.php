<?php

/**
 * The admin-specific logic for the react based admin settings page
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

class Trvlr_Admin_App
{

   /**
    * The ID of this plugin.
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $plugin_name    The ID of this plugin.
    */
   private $plugin_name;

   /**
    * The version of this plugin.
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $version    The current version of this plugin.
    */
   private $version;

   /**
    * Initialize the class and set its properties.
    *
    * @since    1.0.0
    * @param    string    $plugin_name       The name of this plugin.
    * @param    string    $version           The version of this plugin.
    */
   public function __construct($plugin_name, $version)
   {

      $this->plugin_name = $plugin_name;
      $this->version = $version;
   }

   /**
    * Register the stylesheets for the admin area.
    *
    * @since    1.0.0
    */
   public function enqueue_styles()
   {
      $screen = get_current_screen();

      // Enqueue on TRVLR AI admin app page
      if ($screen && $screen->id === 'toplevel_page_trvlr_admin_app') {
         wp_enqueue_style('trvlr-admin-app', plugin_dir_url(__FILE__) . 'css/trvlr-admin-app.css', array(), $this->version, 'all');
      }

      // Enqueue frontend CSS on settings page for preview
      if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
         wp_enqueue_style('trvlr-public', plugin_dir_url(dirname(__FILE__)) . 'public/css/trvlr-public.css', array(), $this->version, 'all');
      }
   }

   /**
    * Register the JavaScript for the admin area.
    *
    * @since    1.0.0
    */
   public function enqueue_scripts()
   {
      $screen = get_current_screen();

      // Enqueue React settings components on settings page
      if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
         // Theme settings component
         $theme_asset = include(plugin_dir_path(__FILE__) . 'build/trvlr-admin-root.jsx.asset.php');
         wp_enqueue_script(
            'trvlr-admin-root',
            plugin_dir_url(__FILE__) . 'build/trvlr-admin-root.jsx.js',
            $theme_asset['dependencies'],
            $theme_asset['version'],
            true
         );

         wp_enqueue_style('wp-components');
      }
   }

   /**
    * Register the administration menu for this plugin into the WordPress Dashboard menu.
    *
    * @since    1.0.0
    */
   public function add_plugin_admin_menu()
   {
      // Add new React-based admin page (non-destructive)
      add_menu_page(
         __('TRVLR AI', 'trvlr'),
         __('TRVLR AI', 'trvlr'),
         'manage_options',
         'trvlr_admin_app',
         array($this, 'display_admin_app_page'),
         'dashicons-admin-generic',
         31
      );
   }

   /**
    * Output SVG icons for attraction card preview
    */
   public function output_admin_svg_icons()
   {
      $screen = get_current_screen();
      if ($screen && $screen->id === 'toplevel_page_trvlr_settings') {
?>
         <svg style="display: none;">
            <symbol id="icon-star" viewBox="0 0 18 18">
               <path d="M9.00002 0.5C9.38064 0.5 9.72803 0.716313 9.8965 1.05762L11.9805 5.28027L16.6446 5.96289C17.0211 6.01793 17.3338 6.28252 17.4512 6.64453C17.5684 7.00643 17.4698 7.40351 17.1973 7.66895L13.8242 10.9531L14.6211 15.5957C14.6855 15.9709 14.5307 16.3505 14.2227 16.5742C13.9148 16.7978 13.5067 16.8273 13.1699 16.6504L9.00002 14.457L4.8301 16.6504C4.49331 16.8273 4.0852 16.7978 3.77736 16.5742C3.46939 16.3505 3.31458 15.9709 3.37893 15.5957L4.17482 10.9531L0.802754 7.66895C0.530236 7.40351 0.431671 7.00643 0.548848 6.64453C0.666226 6.28252 0.978929 6.01793 1.35549 5.96289L6.01857 5.28027L8.10354 1.05762L8.17482 0.935547C8.35943 0.665559 8.66699 0.5 9.00002 0.5Z" />
            </symbol>
            <symbol id="icon-clock" viewBox="0 0 18 18">
               <g clip-path="url(#clip0_133_223)">
                  <path d="M15.5 9C15.5 5.41015 12.5899 2.5 9 2.5C5.41015 2.5 2.5 5.41015 2.5 9C2.5 12.5899 5.41015 15.5 9 15.5C12.5899 15.5 15.5 12.5899 15.5 9ZM17.5 9C17.5 13.6944 13.6944 17.5 9 17.5C4.30558 17.5 0.5 13.6944 0.5 9C0.5 4.30558 4.30558 0.5 9 0.5C13.6944 0.5 17.5 4.30558 17.5 9Z" />
                  <path d="M8 4.5C8 3.94772 8.44772 3.5 9 3.5C9.55228 3.5 10 3.94772 10 4.5V8.38184L12.4473 9.60547C12.9412 9.85246 13.1415 10.4533 12.8945 10.9473C12.6475 11.4412 12.0467 11.6415 11.5527 11.3945L8.55273 9.89453C8.21395 9.72514 8 9.37877 8 9V4.5Z" />
               </g>
               <defs>
                  <clipPath id="clip0_133_223">
                     <rect width="18" height="18" />
                  </clipPath>
               </defs>
            </symbol>
            <symbol id="icon-arrow-right" viewBox="0 0 21 21">
               <path d="M9.83496 4.29285C10.2255 3.90241 10.8585 3.90236 11.249 4.29285L16.791 9.83484C16.7969 9.84072 16.8019 9.84741 16.8076 9.8534C16.8194 9.86578 16.8307 9.87851 16.8418 9.89148C16.8509 9.90206 16.8596 9.91284 16.8682 9.92371C16.879 9.93742 16.8893 9.95142 16.8994 9.9657C17.1465 10.3148 17.143 10.7848 16.8896 11.1307C16.8847 11.1375 16.8801 11.1446 16.875 11.1512C16.8612 11.1691 16.8462 11.1859 16.8311 11.203C16.8259 11.2089 16.8208 11.2148 16.8154 11.2206C16.807 11.2297 16.7999 11.2401 16.791 11.2489L11.249 16.7899C10.8585 17.1804 10.2255 17.1804 9.83496 16.7899C9.44461 16.3994 9.44449 15.7663 9.83496 15.3759L13.668 11.5419H5C4.4478 11.5419 4.00013 11.094 4 10.5419C4 9.98959 4.44772 9.54187 5 9.54187H13.6699L9.83496 5.70691C9.44444 5.31639 9.44444 4.68337 9.83496 4.29285Z" />
            </symbol>
         </svg>
<?php
      }
   }

   /**
    * Render the React admin app page
    */
   public function display_admin_app_page()
   {
      require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/trvlr-admin-app-page.php';
   }
}
