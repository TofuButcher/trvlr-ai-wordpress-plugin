<?php

if (!isset($_GET['trvlr_test']) || $_GET['trvlr_test'] !== 'true') {
	return;
}

// Resolve TRVLR ID and post ID
$post_id  = 0;
$trvlr_id = 0;

if (isset($_GET['trvlr_id'])) {
	$trvlr_id = intval($_GET['trvlr_id']);
} elseif (isset($_GET['post'])) {
	$post_id  = intval($_GET['post']);
	$trvlr_id = intval(get_trvlr_id($post_id));
}

if (!$trvlr_id) {
	echo '<h1>TRVLR Debug</h1>';
	echo '<p>No TRVLR ID found. Pass <code>?trvlr_test=true&trvlr_id=123</code> or open from a TRVLR Attraction edit screen.</p>';
	die();
}

function trvlr_debug_fetch_from_api($attraction_id)
{
	$api_url = 'https://sl.portal.trvlr.ai/api/process/webapi_handler/generic_attraction_with_id';

	$organisation_id = get_option('trvlr_organisation_id', '');
	$origin          = !empty($organisation_id)
		? 'https://' . sanitize_text_field($organisation_id) . '.trvlr.ai'
		: home_url();

	$response = wp_remote_post($api_url, array(
		'headers' => array('Content-Type' => 'application/json', 'Origin' => $origin),
		'body'    => json_encode(array('id' => $attraction_id)),
		'timeout' => 30,
	));

	if (is_wp_error($response)) {
		return array('error' => $response->get_error_message());
	}

	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	if (!empty($data['results'][0])) {
		return $data['results'][0];
	}

	return array('error' => 'No results found', 'raw' => $body);
}

$api_data = trvlr_debug_fetch_from_api($trvlr_id);

if (isset($api_data['error'])) {
	echo '<h1>Error fetching from API</h1>';
	echo '<p>' . esc_html($api_data['error']) . '</p>';
	if (isset($api_data['raw'])) {
		echo '<pre>' . esc_html($api_data['raw']) . '</pre>';
	}
	die();
}

// Resolve post ID if we didn't get it from the URL
if (!$post_id) {
	$query = new WP_Query(array(
		'post_type'      => 'trvlr_attraction',
		'meta_key'       => 'trvlr_id',
		'meta_value'     => $trvlr_id,
		'posts_per_page' => 1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	));
	if ($query->have_posts()) {
		$post_id = $query->posts[0];
	}
}

// Field map — 'transform' is an optional callable applied to the raw API value.
// Fields without 'transform' show raw data only (no "after transform" box).
$field_map = array(
	'post_title' => array(
		'label'     => 'Title',
		'api_key'   => 'title',
		'transform' => 'sanitize_text_field',
	),
	'trvlr_description' => array(
		'label'     => 'Description',
		'api_key'   => 'description',
		'transform' => array('Trvlr_Data_Transform', 'prepare_for_wp_editor'),
	),
	'trvlr_short_description' => array(
		'label'     => 'Short Description',
		'api_key'   => 'short_description',
		'transform' => array('Trvlr_Data_Transform', 'prepare_for_wp_editor'),
	),
	'trvlr_inclusions' => array(
		'label'     => 'Inclusions',
		'api_key'   => 'inclusions',
		'transform' => array('Trvlr_Data_Transform', 'transform_list_field'),
	),
	'trvlr_highlights' => array(
		'label'     => 'Highlights',
		'api_key'   => 'highlights',
		'transform' => array('Trvlr_Data_Transform', 'transform_list_field'),
	),
	'trvlr_additional_info' => array(
		'label'     => 'Additional Info',
		'api_key'   => 'additional_info',
		'transform' => array('Trvlr_Data_Transform', 'prepare_for_wp_editor'),
	),
	'trvlr_duration' => array(
		'label'     => 'Duration',
		'api_key'   => 'duration',
		'transform' => 'sanitize_text_field',
	),
	'trvlr_start_time' => array(
		'label'     => 'Start Time',
		'api_key'   => 'start_time',
		'transform' => 'sanitize_text_field',
	),
	'trvlr_end_time' => array(
		'label'     => 'End Time',
		'api_key'   => 'end_time',
		'transform' => 'sanitize_text_field',
	),
	'trvlr_pricing' => array(
		'label'   => 'Pricing (raw)',
		'api_key' => 'pricing',
	),
	'trvlr_locations' => array(
		'label'   => 'Locations (raw)',
		'api_key' => 'location',
	),
);

function trvlr_debug_get_db_value($post_id, $field_name)
{
	if ($field_name === 'post_title') {
		return get_the_title($post_id);
	}
	return get_post_meta($post_id, $field_name, true);
}

$raw_api_json = json_encode($api_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

function trvlr_debug_fetch_all_from_api()
{
	$api_url         = 'https://sl.portal.trvlr.ai/api/process/webapi_handler/generic_attractions';
	$organisation_id = get_option('trvlr_organisation_id', '');
	$origin          = !empty($organisation_id)
		? 'https://' . sanitize_text_field($organisation_id) . '.trvlr.ai'
		: home_url();

	$response = wp_remote_post($api_url, array(
		'headers' => array('Content-Type' => 'application/json', 'Origin' => $origin),
		'body'    => json_encode(array('page' => 1, 'page_size' => 1000)),
		'timeout' => 60,
	));

	if (is_wp_error($response)) {
		return array('error' => $response->get_error_message());
	}

	return json_decode(wp_remote_retrieve_body($response), true);
}

$all_attractions_data = trvlr_debug_fetch_all_from_api();
$raw_all_json         = json_encode($all_attractions_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
<!DOCTYPE html>
<html>

<head>
	<title>TRVLR Debug — ID: <?php echo $trvlr_id; ?></title>
	<style>
		*,
		*::before,
		*::after {
			box-sizing: border-box;
		}

		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
			padding: 20px;
			background: #f0f2f5;
			margin: 0;
		}

		.container {
			max-width: 1400px;
			margin: 0 auto;
		}

		.card {
			background: white;
			padding: 20px;
			margin-bottom: 20px;
			border-radius: 8px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}

		.header h1 {
			margin: 0 0 15px 0;
			font-size: 22px;
			color: #111;
		}

		.info-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
			gap: 12px;
		}

		.info-item .info-label {
			font-size: 11px;
			color: #888;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.info-item .info-value {
			font-size: 15px;
			color: #111;
			margin-top: 2px;
		}

		.field-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 14px;
			padding-bottom: 10px;
			border-bottom: 2px solid #eee;
		}

		.field-header h3 {
			margin: 0;
			font-size: 15px;
			color: #111;
		}

		.badge {
			padding: 3px 10px;
			border-radius: 10px;
			font-size: 11px;
			font-weight: 700;
		}

		.badge.match {
			background: #d4edda;
			color: #155724;
		}

		.badge.no-match {
			background: #f8d7da;
			color: #721c24;
		}

		.badge.empty {
			background: #e9ecef;
			color: #555;
		}

		.data-box {
			margin-bottom: 12px;
		}

		.data-label {
			font-size: 12px;
			font-weight: 700;
			color: #555;
			margin-bottom: 5px;
		}

		.data-content {
			background: #f8f9fa;
			padding: 12px 14px;
			border-radius: 4px;
			border-left: 3px solid #4a90e2;
			font-family: 'Courier New', monospace;
			font-size: 12px;
			word-break: break-word;
			white-space: pre-wrap;
			max-height: 280px;
			overflow-y: auto;
		}

		.data-content.from-db {
			border-left-color: #28a745;
		}

		.data-content.empty {
			color: #aaa;
			font-style: italic;
		}

		.meta-row {
			display: flex;
			gap: 16px;
			margin-top: 6px;
			font-size: 11px;
			color: #888;
		}

		.meta-row span b {
			color: #555;
		}

		.warning {
			background: #fff8e1;
			border: 1px solid #ffc107;
			padding: 12px 16px;
			border-radius: 6px;
			margin-bottom: 20px;
			font-size: 14px;
		}

		.section-title {
			font-size: 13px;
			font-weight: 700;
			color: #888;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			margin: 0 0 16px 0;
		}

		.copy-btn {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 7px 14px;
			background: #4a90e2;
			color: white;
			border: none;
			border-radius: 5px;
			font-size: 13px;
			font-weight: 600;
			cursor: pointer;
			transition: background 0.15s;
		}

		.copy-btn:hover {
			background: #2c6fbe;
		}

		.copy-btn.copied {
			background: #28a745;
		}

		.api-response-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 14px;
			padding-bottom: 10px;
			border-bottom: 2px solid #eee;
		}

		.api-response-pre {
			background: #1e1e2e;
			color: #cdd6f4;
			padding: 16px;
			border-radius: 6px;
			font-family: 'Courier New', monospace;
			font-size: 12px;
			max-height: 500px;
			overflow-y: auto;
			white-space: pre;
			margin: 0;
		}
	</style>
</head>

<body>
	<div class="container">

		<div class="card header">
			<h1>TRVLR Data Transform Debug</h1>
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
						<div class="info-value"><?php echo esc_html(get_post_status($post_id)); ?></div>
					</div>
				<?php endif; ?>
				<div class="info-item">
					<div class="info-label">Organisation ID</div>
					<div class="info-value"><?php echo esc_html(get_option('trvlr_organisation_id', 'Not Set')); ?></div>
				</div>
			</div>
		</div>

		<?php if (!$post_id): ?>
			<div class="warning">
				<strong>No WordPress post found</strong> — showing API data only. Sync this attraction to compare against the database.
			</div>
		<?php endif; ?>

		<?php foreach ($field_map as $field_name => $field_config):
			$has_transform    = isset($field_config['transform']);
			$api_value_raw    = isset($api_data[$field_config['api_key']]) ? $api_data[$field_config['api_key']] : '';
			$raw_display      = is_array($api_value_raw) ? json_encode($api_value_raw, JSON_PRETTY_PRINT) : (string) $api_value_raw;

			$api_transformed  = ($has_transform && !empty($api_value_raw))
				? call_user_func($field_config['transform'], $api_value_raw)
				: $api_value_raw;
			$transformed_display = is_array($api_transformed) ? json_encode($api_transformed, JSON_PRETTY_PRINT) : (string) $api_transformed;

			$db_value         = $post_id ? trvlr_debug_get_db_value($post_id, $field_name) : null;
			$db_display       = is_array($db_value) ? json_encode($db_value, JSON_PRETTY_PRINT) : (string) $db_value;

			$has_api = $raw_display !== '' && $raw_display !== 'null';
			$has_db  = $db_value !== null && $db_value !== '' && $db_value !== false;

			$match_status = 'empty';
			if ($has_api && $has_db) {
				if ($has_transform) {
					$db_norm = str_replace(array("\r\n", "\r"), "\n", trim($db_display));
					$api_norm = str_replace(array("\r\n", "\r"), "\n", trim($transformed_display));
					$match_status = (md5($db_norm) === md5($api_norm)) ? 'match' : 'no-match';
				} else {
					$match_status = ($db_display === $raw_display) ? 'match' : 'no-match';
				}
			}
		?>
			<div class="card">
				<div class="field-header">
					<h3><?php echo esc_html($field_config['label']); ?></h3>
					<?php if ($post_id): ?>
						<span class="badge <?php echo $match_status; ?>">
							<?php
							if ($match_status === 'match') echo '✓ Match';
							elseif ($match_status === 'no-match') echo '✗ No Match';
							else echo 'Empty / Not Set';
							?>
						</span>
					<?php endif; ?>
				</div>

				<div class="data-box">
					<div class="data-label">API — Raw</div>
					<div class="data-content <?php echo !$has_api ? 'empty' : ''; ?>">
						<?php echo $has_api ? htmlspecialchars($raw_display) : '(empty)'; ?>
					</div>
					<div class="meta-row">
						<span><b>Length:</b> <?php echo strlen($raw_display); ?> chars</span>
						<?php if ($has_transform): ?>
							<span><b>Transform:</b> <?php echo esc_html(is_array($field_config['transform']) ? implode('::', $field_config['transform']) : $field_config['transform']); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<?php if ($has_transform): ?>
					<div class="data-box">
						<div class="data-label">API — After Transform</div>
						<div class="data-content <?php echo !$has_api ? 'empty' : ''; ?>">
							<?php echo $has_api ? htmlspecialchars($transformed_display) : '(empty)'; ?>
						</div>
						<div class="meta-row">
							<span><b>Length:</b> <?php echo strlen($transformed_display); ?> chars</span>
							<span><b>Hash:</b> <?php echo $has_api ? substr(md5(str_replace(array("\r\n", "\r"), "\n", trim($transformed_display))), 0, 12) . '…' : '—'; ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($post_id): ?>
					<div class="data-box">
						<div class="data-label">Database</div>
						<div class="data-content from-db <?php echo !$has_db ? 'empty' : ''; ?>">
							<?php echo $has_db ? htmlspecialchars($db_display) : '(empty)'; ?>
						</div>
						<div class="meta-row">
							<span><b>Length:</b> <?php echo strlen($db_display); ?> chars</span>
							<?php if ($has_transform && $has_db): ?>
								<span><b>Hash:</b> <?php echo substr(md5(str_replace(array("\r\n", "\r"), "\n", trim($db_display))), 0, 12); ?>…</span>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<?php if ($post_id && !empty($api_data['attraction_type'])): ?>
			<div class="card">
				<div class="field-header">
					<h3>Attraction Tags (Taxonomy)</h3>
				</div>
				<div class="data-box">
					<div class="data-label">API — Raw</div>
					<div class="data-content">
						<?php echo htmlspecialchars(implode(', ', $api_data['attraction_type'])); ?>
					</div>
				</div>
				<div class="data-box">
					<div class="data-label">Database</div>
					<?php
					$terms      = get_the_terms($post_id, 'trvlr_attraction_tag');
					$term_names = ($terms && !is_wp_error($terms)) ? array_map(fn($t) => $t->name, $terms) : array();
					?>
					<div class="data-content from-db">
						<?php echo $term_names ? htmlspecialchars(implode(', ', $term_names)) : '(none)'; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($post_id && !empty($api_data['images']['all_images'])): ?>
			<div class="card">
				<div class="field-header">
					<h3>Images</h3>
				</div>
				<div class="data-box">
					<div class="data-label">API — Raw (<?php echo count($api_data['images']['all_images']); ?> images)</div>
					<div class="data-content">
						<?php
						foreach ($api_data['images']['all_images'] as $i => $img) {
							echo ($i + 1) . '. itemUrl: ' . htmlspecialchars($img['itemUrl'] ?? '') . "\n";
							if (!empty($img['largeSizeUrl'])) {
								echo '   largeSizeUrl: ' . htmlspecialchars($img['largeSizeUrl']) . "\n";
							}
							echo "\n";
						}
						?>
					</div>
				</div>
				<div class="data-box">
					<div class="data-label">Database</div>
					<?php
					$media_ids   = get_post_meta($post_id, 'trvlr_media', true);
					$featured_id = get_post_thumbnail_id($post_id);
					?>
					<div class="data-content from-db">
						Gallery: <?php echo is_array($media_ids) ? count($media_ids) : 0; ?> images
						Featured Image: <?php echo $featured_id ? 'Set (ID: ' . $featured_id . ')' : 'Not set'; ?>
						<?php
						if (is_array($media_ids) && !empty($media_ids)) {
							echo "\nGallery:\n";
							foreach ($media_ids as $i => $att_id) {
								echo ($i + 1) . '. ' . htmlspecialchars(wp_get_attachment_url($att_id)) . "\n";
								$src = get_post_meta($att_id, 'trvlr_source_url', true);
								if ($src) echo '   src: ' . htmlspecialchars($src) . "\n";
							}
						}
						if ($featured_id) {
							echo "\nFeatured:\n";
							echo htmlspecialchars(wp_get_attachment_url($featured_id)) . "\n";
							$src = get_post_meta($featured_id, 'trvlr_source_url', true);
							if ($src) echo 'src: ' . htmlspecialchars($src) . "\n";
						}
						?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="card">
			<div class="api-response-header">
				<h3 class="section-title" style="margin:0;">Full API Response</h3>
				<button class="copy-btn" id="copy-api-btn" onclick="copyApiResponse()">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
						<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
					</svg>
					Copy JSON
				</button>
			</div>
			<pre class="api-response-pre" id="api-response-json"><?php echo htmlspecialchars($raw_api_json); ?></pre>
		</div>

		<div class="card">
			<div class="api-response-header">
				<h3 class="section-title" style="margin:0;">All Attractions API Response</h3>
				<button class="copy-btn" id="copy-all-btn" onclick="copyAllResponse()">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
						<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
					</svg>
					Copy JSON
				</button>
			</div>
			<?php if (isset($all_attractions_data['error'])): ?>
				<p style="color:#721c24;">Error: <?php echo esc_html($all_attractions_data['error']); ?></p>
			<?php else: ?>
				<?php if (!empty($all_attractions_data['results'])): ?>
					<p style="font-size:13px;color:#555;margin:0 0 10px;">
						<?php echo count($all_attractions_data['results']); ?> attractions returned
					</p>
				<?php endif; ?>
				<pre class="api-response-pre" id="api-all-json"><?php echo htmlspecialchars($raw_all_json); ?></pre>
			<?php endif; ?>
		</div>

	</div>

	<script>
		const COPY_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>`;
		const CHECK_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;

		function copyToClipboard(text, btnId) {
			navigator.clipboard.writeText(text).then(() => {
				const btn = document.getElementById(btnId);
				btn.classList.add('copied');
				btn.innerHTML = CHECK_ICON + ' Copied!';
				setTimeout(() => {
					btn.classList.remove('copied');
					btn.innerHTML = COPY_ICON + ' Copy JSON';
				}, 2500);
			});
		}

		function copyApiResponse() {
			copyToClipboard(<?php echo json_encode($raw_api_json); ?>, 'copy-api-btn');
		}

		function copyAllResponse() {
			copyToClipboard(<?php echo json_encode($raw_all_json); ?>, 'copy-all-btn');
		}
	</script>
</body>

</html>
<?php
die();
