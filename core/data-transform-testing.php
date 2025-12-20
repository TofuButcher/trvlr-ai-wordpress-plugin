<?php

if (!isset($_GET['test']) || $_GET['test'] !== 'true') {
   return;
}

$trvlr_id = 5220;
if (isset($_GET['trvlr_id'])) {
   $trvlr_id = intval($_GET['trvlr_id']);
}

function fetch_attraction_from_api($attraction_id)
{
   $api_url = 'https://sl.portal.trvlr.ai/api/process/webapi_handler/generic_attraction_with_id';

   $headers = array(
      'Content-Type' => 'application/json',
   );

   $organisation_id = get_option('trvlr_organisation_id', '');
   if (!empty($organisation_id)) {
      $headers['Origin'] = 'https://' . sanitize_text_field($organisation_id) . '.trvlr.ai';
   } else {
      $headers['Origin'] = home_url();
   }

   $response = wp_remote_post($api_url, array(
      'headers' => $headers,
      'body'    => json_encode(array('id' => $attraction_id)),
      'timeout' => 30
   ));

   if (is_wp_error($response)) {
      return array('error' => $response->get_error_message());
   }

   $body = wp_remote_retrieve_body($response);
   $data = json_decode($body, true);

   if (!empty($data['results'][0])) {
      return $data['results'][0];
   }

   return array('error' => 'No results found');
}

$api_data = fetch_attraction_from_api($trvlr_id);

if (isset($api_data['error'])) {
   echo '<h1>Error fetching from API</h1>';
   echo '<p>' . esc_html($api_data['error']) . '</p>';
   die();
}

$post_id = 0;
if (isset($_GET['post_id'])) {
   $post_id = intval($_GET['post_id']);
} else {
   $args = array(
      'post_type' => 'trvlr_attraction',
      'meta_key' => 'trvlr_id',
      'meta_value' => $trvlr_id,
      'posts_per_page' => 1,
      'fields' => 'ids'
   );
   $query = new WP_Query($args);
   if ($query->have_posts()) {
      $post_id = $query->posts[0];
   }
}

$field_map = array(
   'post_title' => array('label' => 'Title', 'api_key' => 'title', 'type' => 'text'),
   'trvlr_description' => array('label' => 'Description', 'api_key' => 'description', 'type' => 'editor'),
   'trvlr_short_description' => array('label' => 'Short Description', 'api_key' => 'short_description', 'type' => 'editor'),
   'trvlr_inclusions' => array('label' => 'Inclusions', 'api_key' => 'inclusions', 'type' => 'editor'),
   'trvlr_highlights' => array('label' => 'Highlights', 'api_key' => 'highlights', 'type' => 'editor'),
   'trvlr_additional_info' => array('label' => 'Additional Info', 'api_key' => 'additional_info', 'type' => 'editor'),
   'trvlr_duration' => array('label' => 'Duration', 'api_key' => 'duration', 'type' => 'text'),
   'trvlr_start_time' => array('label' => 'Start Time', 'api_key' => 'start_time', 'type' => 'text'),
   'trvlr_end_time' => array('label' => 'End Time', 'api_key' => 'end_time', 'type' => 'text'),
   'trvlr_pricing' => array('label' => 'Pricing', 'api_key' => 'pricing', 'type' => 'array'),
   'trvlr_locations' => array('label' => 'Locations', 'api_key' => 'location', 'type' => 'complex'),
);

function prepare_for_wp_editor($content)
{
   if (empty($content)) {
      return '';
   }
   $content = wp_kses_post($content);
   $content = str_replace(array("\r\n", "\r"), "\n", $content);
   $content = preg_replace('#\s*<p[^>]*>\s*#i', '', $content);
   $content = preg_replace('#\s*</p>\s*#i', "\n\n", $content);
   $content = preg_replace('#<br\s*/?>#i', "\n", $content);
   $content = preg_replace('#</?div[^>]*>#i', '', $content);
   $content = preg_replace("/\n{3,}/", "\n\n", $content);
   $lines = explode("\n", $content);
   $lines = array_map('trim', $lines);
   $content = implode("\n", $lines);
   $content = trim($content);
   return $content;
}

function get_api_value($api_data, $api_key)
{
   return isset($api_data[$api_key]) ? $api_data[$api_key] : '';
}

function get_db_value($post_id, $field_name)
{
   if ($field_name === 'post_title') {
      return get_the_title($post_id);
   }
   return get_post_meta($post_id, $field_name, true);
}

?>
<!DOCTYPE html>
<html>

<head>
   <title>Data Transform Test - TRVLR ID: <?php echo $trvlr_id; ?></title>
   <style>
      body {
         font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
         padding: 20px;
         background: #f8f9fa;
      }

      .container {
         max-width: 1400px;
         margin: 0 auto;
      }

      .header {
         background: white;
         padding: 20px;
         margin-bottom: 20px;
         border-radius: 8px;
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .field-section {
         background: white;
         padding: 20px;
         margin-bottom: 20px;
         border-radius: 8px;
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .field-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 15px;
         padding-bottom: 10px;
         border-bottom: 2px solid #e9ecef;
      }

      .field-header h3 {
         margin: 0;
         color: #212529;
      }

      .match-badge {
         padding: 4px 12px;
         border-radius: 12px;
         font-size: 12px;
         font-weight: bold;
      }

      .match-badge.match {
         background: #d4edda;
         color: #155724;
      }

      .match-badge.no-match {
         background: #f8d7da;
         color: #721c24;
      }

      .match-badge.empty {
         background: #e2e3e5;
         color: #383d41;
      }

      .data-box {
         margin-bottom: 15px;
      }

      .data-label {
         font-weight: 600;
         color: #495057;
         margin-bottom: 5px;
         font-size: 14px;
      }

      .data-content {
         background: #f8f9fa;
         padding: 15px;
         border-radius: 4px;
         border-left: 4px solid #007bff;
         font-family: 'Courier New', monospace;
         font-size: 13px;
         word-wrap: break-word;
         max-height: 300px;
         overflow-y: auto;
      }

      .data-content.from-db {
         border-left-color: #28a745;
      }

      .data-content.empty {
         color: #999;
         font-style: italic;
      }

      .stats {
         display: flex;
         gap: 20px;
         margin-top: 10px;
         font-size: 12px;
         color: #6c757d;
      }

      .stat-item {
         display: flex;
         gap: 5px;
      }

      .stat-label {
         font-weight: 600;
      }

      h1 {
         margin: 0 0 10px 0;
         color: #212529;
      }

      .info-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 15px;
         margin-top: 15px;
      }

      .info-item {
         display: flex;
         flex-direction: column;
         gap: 5px;
      }

      .info-label {
         font-size: 12px;
         color: #6c757d;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: 0.5px;
      }

      .info-value {
         font-size: 16px;
         color: #212529;
      }

      .warning {
         background: #fff3cd;
         border: 1px solid #ffc107;
         padding: 15px;
         border-radius: 4px;
         margin-bottom: 20px;
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="header">
         <h1>TRVLR Data Transform Testing</h1>

         <div class="info-grid">
            <div class="info-item">
               <div class="info-label">TRVLR ID</div>
               <div class="info-value"><?php echo $trvlr_id; ?></div>
            </div>
            <div class="info-item">
               <div class="info-label">WordPress Post ID</div>
               <div class="info-value"><?php echo $post_id ? $post_id : 'Not Found'; ?></div>
            </div>
            <?php if ($post_id): ?>
               <div class="info-item">
                  <div class="info-label">Post Title</div>
                  <div class="info-value"><?php echo esc_html(get_the_title($post_id)); ?></div>
               </div>
               <div class="info-item">
                  <div class="info-label">Post Status</div>
                  <div class="info-value"><?php echo get_post_status($post_id); ?></div>
               </div>
            <?php endif; ?>
            <div class="info-item">
               <div class="info-label">Organisation ID</div>
               <div class="info-value"><?php echo get_option('trvlr_organisation_id', 'Not Set'); ?></div>
            </div>
         </div>
      </div>

      <?php if (!$post_id): ?>
         <div class="warning">
            <strong>⚠️ No WordPress post found</strong> - Only showing API data. Create a post with TRVLR ID <?php echo $trvlr_id; ?> to see database comparison.
         </div>
      <?php endif; ?>

      <?php foreach ($field_map as $field_name => $field_config): ?>
         <?php
         $api_value_raw = get_api_value($api_data, $field_config['api_key']);
         $api_value_transformed = ($field_config['type'] === 'editor') ? prepare_for_wp_editor($api_value_raw) : $api_value_raw;
         $db_value = $post_id ? get_db_value($post_id, $field_name) : null;

         $api_value_raw_display = is_array($api_value_raw) ? json_encode($api_value_raw, JSON_PRETTY_PRINT) : $api_value_raw;
         $api_value_transformed_display = $api_value_transformed;
         $db_value_display = $db_value;

         if ($field_config['type'] === 'array' || $field_config['type'] === 'complex') {
            $api_value_transformed_display = json_encode($api_value_transformed, JSON_PRETTY_PRINT);
            $db_value_display = $db_value ? json_encode($db_value, JSON_PRETTY_PRINT) : null;
         }

         $has_api_value = !empty($api_value_transformed) || $api_value_transformed === '0';
         $has_db_value = !empty($db_value) || $db_value === '0';

         $match_status = 'empty';
         if ($has_api_value && $has_db_value) {
            if ($field_config['type'] === 'editor') {
               $db_normalized = str_replace(array("\r\n", "\r"), "\n", trim($db_value));
               $match_status = (md5($db_normalized) === md5($api_value_transformed)) ? 'match' : 'no-match';
            } elseif ($field_config['type'] === 'array' || $field_config['type'] === 'complex') {
               $match_status = (json_encode($db_value) === json_encode($api_value_transformed)) ? 'match' : 'no-match';
            } else {
               $match_status = ($db_value === $api_value_transformed) ? 'match' : 'no-match';
            }
         }
         ?>

         <div class="field-section">
            <div class="field-header">
               <h3><?php echo esc_html($field_config['label']); ?></h3>
               <?php if ($post_id): ?>
                  <span class="match-badge <?php echo $match_status; ?>">
                     <?php
                     if ($match_status === 'match') echo '✓ Match';
                     elseif ($match_status === 'no-match') echo '✗ No Match';
                     else echo 'Empty/Not Set';
                     ?>
                  </span>
               <?php endif; ?>
            </div>

            <div class="data-box">
               <div class="data-label">From API (Raw)</div>
               <div class="data-content <?php echo !$has_api_value ? 'empty' : ''; ?>">
                  <?php echo $has_api_value ? htmlspecialchars($api_value_raw_display) : '(empty)'; ?></div>
               <div class="stats">
                  <div class="stat-item">
                     <span class="stat-label">Length:</span>
                     <span><?php echo strlen($api_value_raw_display); ?> chars</span>
                  </div>
                  <div class="stat-item">
                     <span class="stat-label">Type:</span>
                     <span><?php echo $field_config['type']; ?></span>
                  </div>
               </div>
            </div>

            <?php if ($field_config['type'] === 'editor'): ?>
               <div class="data-box">
                  <div class="data-label">From API (After Transform)</div>
                  <div class="data-content <?php echo !$has_api_value ? 'empty' : ''; ?>">
                     <?php echo $has_api_value ? htmlspecialchars($api_value_transformed_display) : '(empty)'; ?></div>
                  <div class="stats">
                     <div class="stat-item">
                        <span class="stat-label">Length:</span>
                        <span><?php echo strlen($api_value_transformed_display); ?> chars</span>
                     </div>
                     <div class="stat-item">
                        <span class="stat-label">Hash:</span>
                        <span><?php echo substr(md5($api_value_transformed_display), 0, 12); ?>...</span>
                     </div>
                  </div>
               </div>
            <?php endif; ?>

            <?php if ($post_id): ?>
               <div class="data-box">
                  <div class="data-label">From Database (WordPress)</div>
                  <div class="data-content from-db <?php echo !$has_db_value ? 'empty' : ''; ?>">
                     <?php echo $has_db_value ? htmlspecialchars($db_value_display) : '(empty)'; ?></div>
                  <div class="stats">
                     <div class="stat-item">
                        <span class="stat-label">Length:</span>
                        <span><?php echo strlen($db_value_display); ?> chars</span>
                     </div>
                     <?php if ($field_config['type'] === 'editor'): ?>
                        <?php
                        $db_normalized = str_replace(array("\r\n", "\r"), "\n", trim($db_value));
                        ?>
                        <div class="stat-item">
                           <span class="stat-label">Hash (normalized):</span>
                           <span><?php echo substr(md5($db_normalized), 0, 12); ?>...</span>
                        </div>
                     <?php endif; ?>
                  </div>
               </div>
            <?php endif; ?>
         </div>
      <?php endforeach; ?>

      <?php if ($post_id && !empty($api_data['attraction_type'])): ?>
         <div class="field-section">
            <div class="field-header">
               <h3>Attraction Tags (Taxonomy)</h3>
            </div>
            <div class="data-box">
               <div class="data-label">From API</div>
               <div class="data-content">
                  <?php echo htmlspecialchars(implode(', ', $api_data['attraction_type'])); ?></div>
            </div>
            <div class="data-box">
               <div class="data-label">From Database</div>
               <?php
               $terms = get_the_terms($post_id, 'trvlr_attraction_tag');
               $term_names = $terms && !is_wp_error($terms) ? array_map(function ($t) {
                  return $t->name;
               }, $terms) : array();
               ?>
               <div class="data-content from-db">
                  <?php echo $term_names ? htmlspecialchars(implode(', ', $term_names)) : '(none)'; ?></div>
            </div>
         </div>
      <?php endif; ?>

      <?php if ($post_id && !empty($api_data['images']['all_images'])): ?>
         <div class="field-section">
            <div class="field-header">
               <h3>Images</h3>
            </div>
            <div class="data-box">
               <div class="data-label">From API (<?php echo count($api_data['images']['all_images']); ?> images)</div>
               <div class="data-content">
                  <?php
                  foreach ($api_data['images']['all_images'] as $index => $img) {
                     $item_url = $img['itemUrl'] ?? '';
                     $large_url = $img['largeSizeUrl'] ?? '';
                     echo ($index + 1) . ". itemUrl: " . htmlspecialchars($item_url) . "\n";
                     if ($large_url) {
                        echo "   largeSizeUrl: " . htmlspecialchars($large_url) . "\n";
                     }
                     echo "\n";
                  }
                  ?></div>
            </div>
            <div class="data-box">
               <div class="data-label">From Database</div>
               <?php
               $media_ids = get_post_meta($post_id, 'trvlr_media', true);
               $featured_id = get_post_thumbnail_id($post_id);
               ?>
               <div class="data-content from-db">
                  Gallery: <?php echo is_array($media_ids) ? count($media_ids) : 0; ?> images
                  Featured Image: <?php echo $featured_id ? 'Set (ID: ' . $featured_id . ')' : 'Not set'; ?>

                  <?php
                  if (is_array($media_ids) && !empty($media_ids)) {
                     echo "\nGallery Images:\n";
                     foreach ($media_ids as $index => $att_id) {
                        $url = wp_get_attachment_url($att_id);
                        $source_url = get_post_meta($att_id, 'trvlr_source_url', true);
                        echo ($index + 1) . ". WP URL: " . htmlspecialchars($url) . "\n";
                        if ($source_url) {
                           echo "   Source URL: " . htmlspecialchars($source_url) . "\n";
                        }
                        echo "\n";
                     }
                  }

                  if ($featured_id) {
                     $featured_url = wp_get_attachment_url($featured_id);
                     $featured_source = get_post_meta($featured_id, 'trvlr_source_url', true);
                     echo "\nFeatured Image:\n";
                     echo "WP URL: " . htmlspecialchars($featured_url) . "\n";
                     if ($featured_source) {
                        echo "Source URL: " . htmlspecialchars($featured_source) . "\n";
                     }
                  }
                  ?></div>
            </div>
         </div>
      <?php endif; ?>
   </div>
</body>

</html>
<?php
die();
