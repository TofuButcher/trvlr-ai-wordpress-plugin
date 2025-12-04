<?php
// Test data transformation for wp_editor fields
// Access with ?testing=1

if (!isset($_GET['testing']) || $_GET['testing'] != '1') {
   return;
}

$post_id = 845;

$json_file = plugin_dir_path(__FILE__) . '../api/single-attraction.json';
$json_data = file_get_contents($json_file);
$api_data = json_decode($json_data, true);
$raw_inclusions = $api_data['results'][0]['inclusions'] ?? 'No Inclusions found';

$db_inclusions = '';
$db_hash = '';
$db_hash_normalized = '';
$synced_hash = '';
if ($post_id) {
   $db_inclusions = get_post_meta($post_id, 'trvlr_inclusions', true);
   $db_hash = md5(trim($db_inclusions));

   $db_normalized = str_replace(array("\r\n", "\r"), "\n", trim($db_inclusions));
   $db_hash_normalized = md5($db_normalized);

   $synced_hash = get_post_meta($post_id, '_trvlr_sync_hash_trvlr_inclusions', true);
}

function test_transform($content)
{
   if (empty($content)) {
      return '';
   }
   $content = wp_kses_post($content);
   // Normalize line endings to \n
   $content = str_replace(array("\r\n", "\r"), "\n", $content);

   // Strip opening <p> tags
   $content = preg_replace('#\s*<p[^>]*>\s*#i', '', $content);

   // Convert closing </p> tags to double line breaks
   $content = preg_replace('#\s*</p>\s*#i', "\n\n", $content);

   // Convert <br> tags to single line breaks
   $content = preg_replace('#<br\s*/?>#i', "\n", $content);

   // Strip <div> tags
   $content = preg_replace('#</?div[^>]*>#i', '', $content);

   // Collapse excess blank lines
   $content = preg_replace("/\n{3,}/", "\n\n", $content);

   // Trim each line individually (editor strips leading/trailing spaces per line)
   $lines = explode("\n", $content);
   $lines = array_map('trim', $lines);
   $content = implode("\n", $lines);

   // Trim overall
   $content = trim($content);

   return $content;
}

$transformed = test_transform($raw_inclusions);

?>
<!DOCTYPE html>
<html>

<head>
   <title>Data Transform Test</title>
   <style>
      body {
         font-family: monospace;
         padding: 20px;
      }

      .box {
         border: 1px solid #ccc;
         padding: 10px;
         margin: 20px 0;
         background: #f5f5f5;
      }

      h2 {
         color: #333;
      }

      pre {
         white-space: pre-wrap;
         word-wrap: break-word;
      }

      .stats {
         color: #666;
         margin-top: 10px;
      }
   </style>
</head>

<body>
   <h1>Data Transform Test</h1>

   <?php if ($post_id): ?>
      <div class="box" style="background: #e8f5e9;">
         <h2>CURRENT POST DATA (Post ID: <?php echo $post_id; ?>)</h2>
         <p><strong>Post Title:</strong> <?php echo esc_html(get_post($post_id)->post_title); ?></p>
         <p><strong>TRVLR ID:</strong> <?php echo get_post_meta($post_id, 'trvlr_id', true); ?></p>
      </div>
   <?php endif; ?>

   <div class="box">
      <h2>1. BEFORE (Raw from API)</h2>
      <div class="stats">Length: <?php echo strlen($raw_inclusions); ?> chars</div>
      <pre><?php echo htmlspecialchars($raw_inclusions); ?></pre>
   </div>

   <div class="box">
      <h2>2. AFTER TRANSFORM (What sync stores)</h2>
      <div class="stats">Length: <?php echo strlen($transformed); ?> chars</div>
      <pre><?php echo htmlspecialchars($transformed); ?></pre>
   </div>

   <?php if ($post_id && $db_inclusions): ?>
      <div class="box" style="background: #fff3e0;">
         <h2>3. FROM DATABASE (What's actually stored)</h2>
         <div class="stats">Length: <?php echo strlen($db_inclusions); ?> chars</div>
         <pre><?php echo htmlspecialchars($db_inclusions); ?></pre>
      </div>
   <?php endif; ?>

   <div class="box">
      <h2>Hash Comparison</h2>
      <table style="width: 100%; border-collapse: collapse;">
         <tr style="background: #f5f5f5;">
            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Source</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Hash</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Match?</th>
         </tr>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">API Raw</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><code><?php echo md5($raw_inclusions); ?></code></td>
            <td style="padding: 10px; border: 1px solid #ddd;">-</td>
         </tr>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">API Transformed</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><code><?php echo md5($transformed); ?></code></td>
            <td style="padding: 10px; border: 1px solid #ddd;">-</td>
         </tr>
         <?php if ($post_id && $db_inclusions): ?>
            <tr style="background: #fff3e0;">
               <td style="padding: 10px; border: 1px solid #ddd;"><strong>DB Value (raw)</strong></td>
               <td style="padding: 10px; border: 1px solid #ddd;"><code><?php echo $db_hash; ?></code></td>
               <td style="padding: 10px; border: 1px solid #ddd;">
                  <?php if ($db_hash === md5($transformed)): ?>
                     <span style="color: green; font-weight: bold;">✓ MATCH</span>
                  <?php else: ?>
                     <span style="color: red; font-weight: bold;">✗ NO MATCH (has \r\n line endings)</span>
                  <?php endif; ?>
               </td>
            </tr>
            <tr style="background: #c8e6c9;">
               <td style="padding: 10px; border: 1px solid #ddd;"><strong>DB Value (normalized)</strong></td>
               <td style="padding: 10px; border: 1px solid #ddd;"><code><?php echo $db_hash_normalized; ?></code></td>
               <td style="padding: 10px; border: 1px solid #ddd;">
                  <?php if ($db_hash_normalized === md5($transformed)): ?>
                     <span style="color: green; font-weight: bold;">✓ MATCH (line endings fixed!)</span>
                  <?php else: ?>
                     <span style="color: red; font-weight: bold;">✗ STILL NO MATCH</span>
                  <?php endif; ?>
               </td>
            </tr>
            <tr style="background: #e3f2fd;">
               <td style="padding: 10px; border: 1px solid #ddd;"><strong>Synced Hash (stored)</strong></td>
               <td style="padding: 10px; border: 1px solid #ddd;"><code><?php echo $synced_hash; ?></code></td>
               <td style="padding: 10px; border: 1px solid #ddd;">
                  <?php if ($synced_hash === $db_hash_normalized): ?>
                     <span style="color: green; font-weight: bold;">✓ MATCH WITH NORMALIZED DB</span>
                  <?php else: ?>
                     <span style="color: red; font-weight: bold;">✗ NO MATCH</span>
                  <?php endif; ?>
               </td>
            </tr>
         <?php endif; ?>
      </table>
   </div>

   <?php if ($post_id && $db_inclusions && $db_hash !== md5($transformed)): ?>
      <div class="box" style="background: #ffebee;">
         <h2>Character-by-Character Comparison</h2>
         <?php
         $len_transformed = strlen($transformed);
         $len_db = strlen($db_inclusions);
         $max_len = max($len_transformed, $len_db);

         echo "<p><strong>Length Difference:</strong> DB has " . ($len_db - $len_transformed) . " more characters</p>";

         // Find differences
         $differences = [];
         for ($i = 0; $i < $max_len; $i++) {
            $char_t = $i < $len_transformed ? $transformed[$i] : '[END]';
            $char_db = $i < $len_db ? $db_inclusions[$i] : '[END]';

            if ($char_t !== $char_db) {
               $differences[] = [
                  'pos' => $i,
                  'transformed' => $char_t,
                  'db' => $char_db,
                  'transformed_ord' => $char_t !== '[END]' ? ord($char_t) : 'N/A',
                  'db_ord' => $char_db !== '[END]' ? ord($char_db) : 'N/A',
               ];
            }
         }

         if (count($differences) > 0) {
            echo "<p><strong>Found " . count($differences) . " differences:</strong></p>";
            echo "<table style='width: 100%; border-collapse: collapse; font-size: 12px;'>";
            echo "<tr style='background: #f5f5f5;'>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>Position</th>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>Transformed</th>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>DB</th>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>Ord (Transformed)</th>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>Ord (DB)</th>";
            echo "<th style='padding: 5px; border: 1px solid #ddd;'>Context</th>";
            echo "</tr>";

            foreach (array_slice($differences, 0, 20) as $diff) {
               $context_start = max(0, $diff['pos'] - 20);
               $context_len = 40;
               $context_t = substr($transformed, $context_start, $context_len);
               $context_db = substr($db_inclusions, $context_start, $context_len);

               echo "<tr>";
               echo "<td style='padding: 5px; border: 1px solid #ddd;'>" . $diff['pos'] . "</td>";
               echo "<td style='padding: 5px; border: 1px solid #ddd;'><code>" . htmlspecialchars($diff['transformed']) . "</code></td>";
               echo "<td style='padding: 5px; border: 1px solid #ddd;'><code>" . htmlspecialchars($diff['db']) . "</code></td>";
               echo "<td style='padding: 5px; border: 1px solid #ddd;'>" . $diff['transformed_ord'] . "</td>";
               echo "<td style='padding: 5px; border: 1px solid #ddd;'>" . $diff['db_ord'] . "</td>";
               echo "<td style='padding: 5px; border: 1px solid #ddd; font-size: 10px;'>" . htmlspecialchars($context_db) . "</td>";
               echo "</tr>";
            }

            echo "</table>";

            if (count($differences) > 20) {
               echo "<p><em>... and " . (count($differences) - 20) . " more differences</em></p>";
            }
         }
         ?>
      </div>
   <?php endif; ?>

</body>

</body>

</html>
<?php
die();
