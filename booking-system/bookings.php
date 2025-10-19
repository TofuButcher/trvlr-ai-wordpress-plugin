<?php

function trvlr_inject_booking_modals()
{
   $base_iframe_url = get_option('trvlr_base_domain', '');
   $frontend_enabled = get_option('trvlr_enable_frontend', true);

   if (empty($base_iframe_url) || !$frontend_enabled) {
      return;
   }
?>
   <dialog id="trvlr-booking-modal" class="modal-dialog">
      <div id="trvlr-booking-modal-content" class="iframe-cont">
      </div>
   </dialog>

   <div id="checkout-modal-iframe" style="display: none">
      <iframe src="<?php echo esc_url($base_iframe_url); ?>/checkout-modal/index.html" frameborder="0" title="Checkout Modal"
         class="iframe-cont" id="checkout-modal-btn-iframe">
      </iframe>
   </div>
<?php
}
add_action('wp_footer', 'trvlr_inject_booking_modals');

function trvlr_render_payment_confirmation_page()
{
   $base_iframe_url = get_option('trvlr_base_domain', '');

   if (empty($base_iframe_url)) {
      return '<p>Please configure the trvlr base domain in the plugin settings.</p>';
   }

   ob_start();
?>
   <div id="payment-confirmation-container" class="payment-confirmation-wrapper">
   </div>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         console.log('DOMContentLoaded for payment confirmation page');
         const container = document.getElementById('payment-confirmation-container');
         if (container) {
            container.innerHTML = `
               <iframe
                  id="payment-confirmation-iframe"
                  style="width: 100%; height: 100vh; border: none;"
                  frameborder="0"
                  src="<?php echo esc_url($base_iframe_url); ?>/payment/confirmation.html"
                  title="Payment Confirmation"
                  class="iframe-cont"
               ></iframe>
            `;
         }

         window.addEventListener('message', function(event) {
            console.log('Payment confirmation message received:', event.data);

            if (event.data.type === 'REFRESH_PAGE') {
               console.log('Setting refresh page flag in localStorage');
               localStorage.setItem('isRefreshPage', 'true');

               setTimeout(function() {
                  window.location.href = '<?php echo esc_url(home_url()); ?>';
               }, 2000);
            }
         });
      });
   </script>
<?php
   return ob_get_clean();
}
add_shortcode('trvlr_payment_confirmation', 'trvlr_render_payment_confirmation_page');

add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
   if (!is_page()) {
      return $redirect_url;
   }

   $page = get_page_by_path('payments');
   if (!$page || get_queried_object_id() !== (int) $page->ID) {
      return $redirect_url;
   }

   if (!empty($_GET)) {
      return false;
   }

   return $redirect_url;
}, 10, 2);

function trvlr_render_booking_calendar($atts)
{
   $base_iframe_url = get_option('trvlr_base_domain', '');

   if (empty($base_iframe_url)) {
      return '<p>Sorry, no trvlr domain configured.</p>';
   }

   if (empty($atts['attraction_id'])) {
      return '<p>Sorry, no attraction id found.</p>';
   }

   ob_start();
?>
   <iframe id='trvlr-booking-calendar-iframe'
      style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;"
      frameborder="0"
      src="<?php echo esc_url($base_iframe_url); ?>/date-picker2/index.html?attr_id=<?php echo esc_attr($atts['attraction_id']); ?>"
      title="Booking Calendar"
      class="iframe-cont"></iframe>
<?php
   return ob_get_clean();
}

function output_booking_calendar_shortcode($atts)
{
   $atts = shortcode_atts(array(
      'attraction_id' => get_field("attraction_id", get_the_ID()),
      'width' => '450px',
      'height' => '600px'
   ), $atts);

   echo trvlr_render_booking_calendar($atts);
}
add_shortcode('trvlr_booking_calendar', 'output_booking_calendar_shortcode');
