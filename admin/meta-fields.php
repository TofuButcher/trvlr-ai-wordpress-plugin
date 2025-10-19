<?php

if (!defined('ABSPATH')) {
   exit;
}

function trvlr_register_attraction_id_meta()
{
   $post_types_option = get_option('trvlr_tour_post_types', '');

   if (empty($post_types_option)) {
      return;
   }

   $post_types = array_map('trim', explode(',', $post_types_option));
   $post_types = array_filter($post_types);

   foreach ($post_types as $post_type) {
      if (!post_type_exists($post_type)) {
         continue;
      }

      register_post_meta($post_type, 'attraction_id', array(
         'type' => 'string',
         'description' => 'Trvlr Attraction ID',
         'single' => true,
         'show_in_rest' => true,
         'auth_callback' => function () {
            return current_user_can('edit_posts');
         }
      ));

      add_action('add_meta_boxes_' . $post_type, 'trvlr_add_attraction_id_meta_box');
   }
}
add_action('init', 'trvlr_register_attraction_id_meta', 20);

function trvlr_add_attraction_id_meta_box($post)
{
   $existing_value = trvlr_get_attraction_id($post->ID);

   if ($existing_value) {
      return;
   }

   add_meta_box(
      'trvlr_attraction_id',
      'Trvlr Attraction ID',
      'trvlr_attraction_id_meta_box_callback',
      null,
      'side',
      'default'
   );
}

function trvlr_attraction_id_meta_box_callback($post)
{
   wp_nonce_field('trvlr_save_attraction_id', 'trvlr_attraction_id_nonce');

   $value = get_post_meta($post->ID, 'attraction_id', true);

?>
   <p>
      <label for="trvlr_attraction_id_field">Attraction ID:</label><br>
      <input type="text"
         id="trvlr_attraction_id_field"
         name="trvlr_attraction_id"
         value="<?php echo esc_attr($value); ?>"
         class="widefat">
   </p>
   <p class="description">Enter the trvlr.ai attraction ID for this post.</p>
<?php
}

function trvlr_save_attraction_id_meta($post_id)
{
   if (!isset($_POST['trvlr_attraction_id_nonce'])) {
      return;
   }

   if (!wp_verify_nonce($_POST['trvlr_attraction_id_nonce'], 'trvlr_save_attraction_id')) {
      return;
   }

   if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
   }

   if (!current_user_can('edit_post', $post_id)) {
      return;
   }

   if (isset($_POST['trvlr_attraction_id'])) {
      $value = sanitize_text_field($_POST['trvlr_attraction_id']);
      update_post_meta($post_id, 'attraction_id', $value);
   }
}
add_action('save_post', 'trvlr_save_attraction_id_meta');

function trvlr_get_attraction_id($post_id)
{
   if (function_exists('get_field')) {
      $acf_value = get_field('attraction_id', $post_id);
      if ($acf_value) {
         return $acf_value;
      }
   }

   $meta_value = get_post_meta($post_id, 'attraction_id', true);
   return $meta_value ? $meta_value : '';
}
