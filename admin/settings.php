<?php

if (!defined('ABSPATH')) {
   exit;
}

function trvlr_add_admin_menu()
{
   $svg_icon = '<svg width="39" height="9" viewBox="0 0 39 9" fill="none" xmlns="http://www.w3.org/2000/svg">
               <path d="M0.323864 2.18182V0.272727H7.90909V2.18182H5.28409V9H2.94886V2.18182H0.323864ZM8.44078 9V0.272727H12.2078C12.8556 0.272727 13.4223 0.390625 13.9081 0.62642C14.3939 0.862216 14.7717 1.2017 15.0416 1.64489C15.3115 2.08807 15.4465 2.61932 15.4465 3.23864C15.4465 3.86364 15.3073 4.39062 15.0288 4.8196C14.7533 5.24858 14.3655 5.57244 13.8655 5.79119C13.3683 6.00994 12.7874 6.11932 12.1226 6.11932H9.8726V4.27841H11.6453C11.9237 4.27841 12.161 4.24432 12.357 4.17614C12.5558 4.10511 12.7078 3.9929 12.8129 3.83949C12.9209 3.68608 12.9749 3.4858 12.9749 3.23864C12.9749 2.98864 12.9209 2.78551 12.8129 2.62926C12.7078 2.47017 12.5558 2.35369 12.357 2.27983C12.161 2.20312 11.9237 2.16477 11.6453 2.16477H10.8101V9H8.44078ZM13.5544 4.99432L15.7362 9H13.1624L11.0317 4.99432H13.5544ZM17.8532 0.272727L19.643 6.42614H19.7112L21.501 0.272727H24.1771L21.2964 9H18.0578L15.1771 0.272727H17.8532ZM24.6577 9V0.272727H27.027V7.09091H30.5554V9H24.6577ZM31.1531 9V0.272727H34.9202C35.5679 0.272727 36.1347 0.390625 36.6205 0.62642C37.1063 0.862216 37.4841 1.2017 37.754 1.64489C38.0239 2.08807 38.1588 2.61932 38.1588 3.23864C38.1588 3.86364 38.0196 4.39062 37.7412 4.8196C37.4656 5.24858 37.0778 5.57244 36.5778 5.79119C36.0807 6.00994 35.4997 6.11932 34.8349 6.11932H32.5849V4.27841H34.3577C34.6361 4.27841 34.8733 4.24432 35.0693 4.17614C35.2682 4.10511 35.4202 3.9929 35.5253 3.83949C35.6332 3.68608 35.6872 3.4858 35.6872 3.23864C35.6872 2.98864 35.6332 2.78551 35.5253 2.62926C35.4202 2.47017 35.2682 2.35369 35.0693 2.27983C34.8733 2.20312 34.6361 2.16477 34.3577 2.16477H33.5224V9H31.1531ZM36.2668 4.99432L38.4486 9H35.8747L33.744 4.99432H36.2668Z" fill="url(#paint0_linear_57_207)"/>
               <defs>
               <linearGradient id="paint0_linear_57_207" x1="39" y1="5" x2="1.09449e-07" y2="5" gradientUnits="userSpaceOnUse">
               <stop stop-color="#006EFA"/>
               <stop offset="1" stop-color="#0BBAE1"/>
               </linearGradient>
               </defs>
               </svg>';

   add_menu_page(
      'Trvlr Settings',
      'Trvlr Settings',
      'manage_options',
      'trvlr-settings',
      'trvlr_settings_page',
      'data:image/svg+xml;base64,' . base64_encode($svg_icon),
      30
   );
}
add_action('admin_menu', 'trvlr_add_admin_menu');

function trvlr_settings_init()
{
   register_setting('trvlr_settings', 'trvlr_base_domain');
   register_setting('trvlr_settings', 'trvlr_enable_frontend', array(
      'type' => 'boolean',
      'default' => true
   ));

   add_settings_section(
      'trvlr_settings_section',
      'Booking System Configuration',
      'trvlr_settings_section_callback',
      'trvlr_settings'
   );

   add_settings_field(
      'trvlr_base_domain',
      'Base Domain',
      'trvlr_base_domain_render',
      'trvlr_settings',
      'trvlr_settings_section'
   );

   add_settings_field(
      'trvlr_enable_frontend',
      'Enable Frontend',
      'trvlr_enable_frontend_render',
      'trvlr_settings',
      'trvlr_settings_section'
   );
}
add_action('admin_init', 'trvlr_settings_init');

function trvlr_base_domain_render()
{
   $value = get_option('trvlr_base_domain', '');
?>
   <input type="text" name="trvlr_base_domain" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://example.trvlr.ai">
   <p class="description">Enter the base domain for your trvlr booking system iframes (e.g., https://yourdomain.trvlr.ai)</p>
<?php
}

function trvlr_enable_frontend_render()
{
   $value = get_option('trvlr_enable_frontend', true);
?>
   <label>
      <input type="checkbox" name="trvlr_enable_frontend" value="1" <?php checked($value, true); ?>>
      Enable booking modals and frontend JavaScript
   </label>
   <p class="description">Uncheck this to disable the plugin's frontend elements, allowing you to develop custom integrations.</p>
<?php
}

function trvlr_settings_section_callback()
{
   echo '<p>Configure the base domain used for trvlr booking system iframes.</p>';
}

function trvlr_settings_page()
{
?>
   <div class="wrap">
      <h1>Trvlr Booking System Settings</h1>
      <form action="options.php" method="post">
         <?php
         settings_fields('trvlr_settings');
         do_settings_sections('trvlr_settings');
         submit_button();
         ?>
      </form>
   </div>
<?php
}
