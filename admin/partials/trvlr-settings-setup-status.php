<?php

/**
 * Setup Status Section
 * 
 * @package Trvlr
 */

if (! defined('ABSPATH')) exit;

$payment_page_id = get_option('trvlr_payment_page_id');
$payment_page_exists = false;
$payment_page_url = '';

if ($payment_page_id) {
   $page = get_post($payment_page_id);
   if ($page && $page->post_status === 'publish') {
      $payment_page_exists = true;
      $payment_page_url = get_permalink($payment_page_id);
   }
}

?>

<div class="trvlr-status-section">
   <h3><?php esc_html_e('System Status', 'trvlr'); ?></h3>
   
   <div class="trvlr-status-grid">
      
      <!-- Payment Page Status -->
      <div class="trvlr-status-item">
         <div class="trvlr-status-label">
            <span class="dashicons dashicons-admin-page"></span>
            <?php esc_html_e('Payment Confirmation Page', 'trvlr'); ?>
         </div>
         <div class="trvlr-status-value">
            <?php if ($payment_page_exists) : ?>
               <span class="trvlr-status-success">
                  <span class="dashicons dashicons-yes-alt"></span>
                  <?php esc_html_e('Active', 'trvlr'); ?>
               </span>
               <a href="<?php echo esc_url($payment_page_url); ?>" target="_blank" class="button button-small">
                  <?php esc_html_e('View Page', 'trvlr'); ?>
               </a>
            <?php else : ?>
               <span class="trvlr-status-error">
                  <span class="dashicons dashicons-warning"></span>
                  <?php esc_html_e('Not Found', 'trvlr'); ?>
               </span>
               <button id="trvlr-create-payment-page" class="button button-primary button-small">
                  <?php esc_html_e('Create Page', 'trvlr'); ?>
               </button>
               <span id="trvlr-payment-page-status" style="margin-left: 10px;"></span>
            <?php endif; ?>
         </div>
      </div>
      
      <!-- API Connection Status - Placeholder -->
      <div class="trvlr-status-item">
         <div class="trvlr-status-label">
            <span class="dashicons dashicons-cloud"></span>
            <?php esc_html_e('API Connection', 'trvlr'); ?>
         </div>
         <div class="trvlr-status-value">
            <span class="trvlr-status-info">
               <span class="dashicons dashicons-info"></span>
               <?php esc_html_e('Not Tested', 'trvlr'); ?>
            </span>
         </div>
      </div>
      
   </div>
</div>

<style>
.trvlr-status-section {
   background: #f8f9fa;
   border: 1px solid #e1e4e8;
   border-radius: 6px;
   padding: 20px;
   margin-bottom: 30px;
}

.trvlr-status-section h3 {
   margin: 0 0 15px 0;
   font-size: 16px;
   color: #1d2327;
}

.trvlr-status-grid {
   display: flex;
   flex-direction: column;
   gap: 15px;
}

.trvlr-status-item {
   display: flex;
   justify-content: space-between;
   align-items: center;
   padding: 12px;
   background: #fff;
   border: 1px solid #e1e4e8;
   border-radius: 4px;
}

.trvlr-status-label {
   display: flex;
   align-items: center;
   gap: 8px;
   font-weight: 500;
   color: #1d2327;
}

.trvlr-status-label .dashicons {
   color: #666;
}

.trvlr-status-value {
   display: flex;
   align-items: center;
   gap: 10px;
}

.trvlr-status-success {
   display: flex;
   align-items: center;
   gap: 5px;
   color: #00a32a;
   font-weight: 500;
}

.trvlr-status-success .dashicons {
   color: #00a32a;
}

.trvlr-status-error {
   display: flex;
   align-items: center;
   gap: 5px;
   color: #d63638;
   font-weight: 500;
}

.trvlr-status-error .dashicons {
   color: #d63638;
}

.trvlr-status-info {
   display: flex;
   align-items: center;
   gap: 5px;
   color: #666;
}

.trvlr-status-info .dashicons {
   color: #666;
}
</style>

