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
} elseif (is_singular('trvlr_attraction')) {
	$post_id  = (int) get_queried_object_id();
	$trvlr_id = intval(get_trvlr_id($post_id));
}

if (!$trvlr_id) {
	echo '<h1>TRVLR Debug</h1>';
	echo '<p>No TRVLR ID found. Use <code>?trvlr_test=true&amp;trvlr_id=123</code>, open from a TRVLR Attraction edit URL (<code>post.php?post=…</code>), or view a single TRVLR Attraction on the frontend with <code>?trvlr_test=true</code>.</p>';
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
		'transform' => array('Trvlr_Data_Transform', 'normalize_post_title_for_sync'),
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
		'label'     => 'Pricing',
		'api_key'   => 'pricing',
		'transform' => array('Trvlr_Data_Transform', 'build_pricing_rows_from_api'),
	),
	'trvlr_locations' => array(
		'label'                 => 'Locations',
		'api_key'               => 'location',
		'transform'             => array('Trvlr_Data_Transform', 'build_location_rows_from_api'),
		'transform_uses_full_api' => true,
	),
);

function trvlr_debug_get_db_value($post_id, $field_name)
{
	if ($field_name === 'post_title') {
		return get_the_title($post_id);
	}
	return get_post_meta($post_id, $field_name, true);
}

function trvlr_debug_copy_btn($plaintext)
{
	$b64 = base64_encode((string) $plaintext);
	echo '<button type="button" class="copy-btn-small" title="Copy" aria-label="Copy" data-trvlr-copy-b64="' . esc_attr($b64) . '">Copy</button>';
}

function trvlr_debug_format_display($value)
{
	if (is_array($value)) {
		return trim(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
	return trim((string) $value, " \t\n\r\0\x0B");
}

function trvlr_debug_api_image_url($img)
{
	if (is_string($img)) {
		if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $img, $m)) {
			return preg_replace('/\.' . preg_quote($m[1], '/') . '$/i', '_lg.' . $m[1], $img);
		}
		return $img;
	}
	if (!is_array($img)) {
		return '';
	}
	if (!empty($img['largeSizeUrl'])) {
		return $img['largeSizeUrl'];
	}
	if (!empty($img['itemUrl'])) {
		return $img['itemUrl'];
	}
	if (!empty($img['url'])) {
		return $img['url'];
	}
	return '';
}

function trvlr_debug_build_images_to_process($api_data)
{
	if (!is_array($api_data)) {
		return array();
	}
	$out = array();
	if (!empty($api_data['list_image'])) {
		$list_url = trvlr_debug_api_image_url($api_data['list_image']);
		if ($list_url !== '') {
			$out[] = array('url' => $list_url);
		}
	}
	if (!empty($api_data['images']['all_images']) && is_array($api_data['images']['all_images'])) {
		$out = array_merge($out, $api_data['images']['all_images']);
	}
	return $out;
}

function trvlr_debug_resolve_media_from_images_to_process($images_to_process)
{
	$gallery_ids    = array();
	$processed_urls = array();
	$first_image_id = null;
	if (empty($images_to_process) || !is_array($images_to_process)) {
		return array(
			'gallery_ids'    => $gallery_ids,
			'featured_id'    => $first_image_id,
		);
	}
	global $wpdb;
	foreach ($images_to_process as $index => $img) {
		$image_url = trvlr_debug_api_image_url($img);
		if ($image_url === '') {
			continue;
		}
		if (in_array($image_url, $processed_urls, true)) {
			continue;
		}
		$processed_urls[] = $image_url;
		$attachment_id    = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'trvlr_source_url' AND meta_value = %s LIMIT 1",
				$image_url
			)
		);
		if (!$attachment_id) {
			continue;
		}
		$attachment_id = (int) $attachment_id;
		$gallery_ids[] = $attachment_id;
		if ($index === 0) {
			$first_image_id = $attachment_id;
		}
	}
	return array(
		'gallery_ids' => $gallery_ids,
		'featured_id' => $first_image_id,
	);
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
		$msg = $response->get_error_message();
		return array(
			'ok'      => false,
			'message' => $msg !== '' ? $msg : 'HTTP request failed (no message)',
			'code'    => 0,
			'raw'     => '',
		);
	}

	$code = wp_remote_retrieve_response_code($response);
	$body = wp_remote_retrieve_body($response);

	if ($body === '' || $body === null) {
		return array(
			'ok'      => false,
			'message' => 'Empty response body (HTTP ' . (int) $code . ')',
			'code'    => (int) $code,
			'raw'     => '',
		);
	}

	$data = json_decode($body, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return array(
			'ok'      => false,
			'message' => 'Invalid JSON: ' . json_last_error_msg() . ' (HTTP ' . (int) $code . ')',
			'code'    => (int) $code,
			'raw'     => substr($body, 0, 4000),
		);
	}

	if (!is_array($data)) {
		return array(
			'ok'      => false,
			'message' => 'Decoded response was not an array/object (HTTP ' . (int) $code . ')',
			'code'    => (int) $code,
			'raw'     => substr($body, 0, 4000),
		);
	}

	return array(
		'ok'      => true,
		'message' => '',
		'code'    => (int) $code,
		'raw'     => '',
		'data'    => $data,
	);
}

$all_fetch_result = trvlr_debug_fetch_all_from_api();
if ($all_fetch_result['ok']) {
	$all_attractions_data = $all_fetch_result['data'];
	$raw_all_json         = json_encode($all_attractions_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	$all_fetch_error      = '';
} else {
	$all_attractions_data = null;
	$all_fetch_error      = $all_fetch_result['message'];
	$raw_all_json         = json_encode(
		array(
			'_trvlr_debug_fetch_failed' => true,
			'message'                   => $all_fetch_result['message'],
			'http_code'                 => $all_fetch_result['code'],
			'body_preview'              => $all_fetch_result['raw'],
		),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
}

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

		.data-box-head {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 8px;
			margin-bottom: 5px;
		}

		.data-box-head .data-label {
			margin-bottom: 0;
		}

		.data-label {
			font-size: 12px;
			font-weight: 700;
			color: #555;
			margin-bottom: 5px;
		}

		.copy-btn-small {
			flex-shrink: 0;
			padding: 2px 8px;
			font-size: 11px;
			line-height: 1.3;
			border: 1px solid #ced4da;
			background: #fff;
			border-radius: 4px;
			cursor: pointer;
			color: #495057;
		}

		.copy-btn-small:hover {
			background: #e9ecef;
			border-color: #adb5bd;
		}

		.copy-btn-small.copied {
			background: #d4edda;
			border-color: #28a745;
			color: #155724;
		}

		.data-content {
			background: #f8f9fa;
			padding: 8px 10px;
			border-radius: 4px;
			border-left: 3px solid #4a90e2;
			font-family: 'Courier New', monospace;
			font-size: 12px;
			word-break: break-word;
			white-space: pre-wrap;
			max-height: 280px;
			overflow-y: auto;
			margin: 0;
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
			$has_transform       = isset($field_config['transform']);
			$transform_full_api  = !empty($field_config['transform_uses_full_api']);
			$api_value_raw       = isset($api_data[$field_config['api_key']]) ? $api_data[$field_config['api_key']] : '';

			$raw_display = trvlr_debug_format_display($api_value_raw);

			if ($has_transform) {
				if ($transform_full_api) {
					$api_transformed = call_user_func($field_config['transform'], $api_data);
				} elseif ($field_name === 'trvlr_pricing') {
					$api_transformed = call_user_func(
						$field_config['transform'],
						is_array($api_value_raw) ? $api_value_raw : array()
					);
				} else {
					$api_transformed = !empty($api_value_raw) || $api_value_raw === '0'
						? call_user_func($field_config['transform'], $api_value_raw)
						: ($field_name === 'post_title' ? call_user_func($field_config['transform'], '') : '');
				}
			} else {
				$api_transformed = $api_value_raw;
			}

			$transformed_display = trvlr_debug_format_display($api_transformed);

			$db_value   = $post_id ? trvlr_debug_get_db_value($post_id, $field_name) : null;
			$db_display = trvlr_debug_format_display($db_value !== null ? $db_value : '');

			$api_empty = ($api_value_raw === '' || $api_value_raw === null || $api_value_raw === false
				|| (is_array($api_value_raw) && empty($api_value_raw)));
			$db_empty  = ($db_value === null || $db_value === '' || $db_value === false
				|| (is_array($db_value) && empty($db_value)));

			$has_api = !$api_empty;
			$has_db  = !$db_empty;

			$match_status = 'empty';
			if ($post_id) {
				if ($api_empty && $db_empty) {
					$match_status = 'match';
				} elseif ($api_empty || $db_empty) {
					$match_status = 'no-match';
				} elseif ($has_transform) {
					$match_status = (Trvlr_Field_Map::hash_field_value($api_transformed, $field_name)
						=== Trvlr_Field_Map::hash_field_value($db_value, $field_name)) ? 'match' : 'no-match';
				} else {
					$match_status = (Trvlr_Field_Map::hash_field_value($api_value_raw, $field_name)
						=== Trvlr_Field_Map::hash_field_value($db_value, $field_name)) ? 'match' : 'no-match';
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
					<div class="data-box-head">
						<div class="data-label">API — Raw</div>
						<?php trvlr_debug_copy_btn($has_api ? $raw_display : '(empty)'); ?>
					</div>
					<div class="data-content <?php echo !$has_api ? 'empty' : ''; ?>"><?php echo $has_api ? htmlspecialchars($raw_display) : '(empty)'; ?></div>
					<div class="meta-row">
						<span><b>Length:</b> <?php echo strlen($raw_display); ?> chars</span>
						<?php if ($has_transform): ?>
							<span><b>Transform:</b> <?php echo esc_html(is_array($field_config['transform']) ? implode('::', $field_config['transform']) : $field_config['transform']); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<?php if ($has_transform): ?>
					<div class="data-box">
						<div class="data-box-head">
							<div class="data-label">API — After Transform</div>
							<?php trvlr_debug_copy_btn($has_api ? $transformed_display : '(empty)'); ?>
						</div>
						<div class="data-content <?php echo !$has_api ? 'empty' : ''; ?>"><?php echo $has_api ? htmlspecialchars($transformed_display) : '(empty)'; ?></div>
						<div class="meta-row">
							<span><b>Length:</b> <?php echo strlen($transformed_display); ?> chars</span>
							<span><b>Hash:</b> <?php echo $has_api ? substr(Trvlr_Field_Map::hash_field_value($api_transformed, $field_name), 0, 12) . '…' : '—'; ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($post_id): ?>
					<div class="data-box">
						<div class="data-box-head">
							<div class="data-label">Database</div>
							<?php trvlr_debug_copy_btn($has_db ? $db_display : '(empty)'); ?>
						</div>
						<div class="data-content from-db <?php echo !$has_db ? 'empty' : ''; ?>"><?php echo $has_db ? htmlspecialchars($db_display) : '(empty)'; ?></div>
						<div class="meta-row">
							<span><b>Length:</b> <?php echo strlen($db_display); ?> chars</span>
							<?php if ($has_transform && $has_db): ?>
								<span><b>Hash:</b> <?php echo substr(Trvlr_Field_Map::hash_field_value($db_value, $field_name), 0, 12); ?>…</span>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<?php if ($post_id && !empty($api_data['attraction_type'])):
			$tags_api_plain = implode(', ', $api_data['attraction_type']);
			$terms          = get_the_terms($post_id, 'trvlr_attraction_tag');
			$term_names     = ($terms && !is_wp_error($terms)) ? array_map(fn($t) => $t->name, $terms) : array();
			$tags_db_plain  = $term_names ? implode(', ', $term_names) : '(none)';
			?>
			<div class="card">
				<div class="field-header">
					<h3>Attraction Tags (Taxonomy)</h3>
				</div>
				<div class="data-box">
					<div class="data-box-head">
						<div class="data-label">API — Raw</div>
						<?php trvlr_debug_copy_btn($tags_api_plain); ?>
					</div>
					<div class="data-content"><?php echo htmlspecialchars($tags_api_plain); ?></div>
				</div>
				<div class="data-box">
					<div class="data-box-head">
						<div class="data-label">Database</div>
						<?php trvlr_debug_copy_btn($tags_db_plain); ?>
					</div>
					<div class="data-content from-db"><?php echo htmlspecialchars($tags_db_plain); ?></div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($post_id && !empty($api_data['images']['all_images'])):
			$media_ids = get_post_meta($post_id, 'trvlr_media', true);
			$img_process = trvlr_debug_build_images_to_process($api_data);
			$img_expected  = trvlr_debug_resolve_media_from_images_to_process($img_process);
			$db_gallery = is_array($media_ids) ? array_values(array_map('intval', $media_ids)) : array();
			$exp_gallery = $img_expected['gallery_ids'];
			sort($exp_gallery);
			sort($db_gallery);
			$db_thumb      = (int) get_post_thumbnail_id($post_id);
			$exp_thumb     = !empty($img_expected['featured_id']) ? (int) $img_expected['featured_id'] : 0;
			$images_match  = (Trvlr_Field_Map::hash_field_value($exp_gallery, 'trvlr_media')
					=== Trvlr_Field_Map::hash_field_value($db_gallery, 'trvlr_media'))
				&& (Trvlr_Field_Map::hash_field_value($exp_thumb, '_thumbnail_id')
					=== Trvlr_Field_Map::hash_field_value($db_thumb, '_thumbnail_id'));
			$images_badge = $images_match ? 'match' : 'no-match';

			$img_api_lines = array();
			foreach ($api_data['images']['all_images'] as $i => $img) {
				$primary = trvlr_debug_api_image_url($img);
				$img_api_lines[] = ($i + 1) . '. ' . ($primary !== '' ? $primary : '(no url in item)');
				if (is_array($img)) {
					if (!empty($img['url']) && $img['url'] !== $primary) {
						$img_api_lines[] = '   url: ' . $img['url'];
					}
					if (!empty($img['itemUrl']) && $img['itemUrl'] !== $primary) {
						$img_api_lines[] = '   itemUrl: ' . $img['itemUrl'];
					}
					if (!empty($img['largeSizeUrl']) && $img['largeSizeUrl'] !== $primary) {
						$img_api_lines[] = '   largeSizeUrl: ' . $img['largeSizeUrl'];
					}
				}
				$img_api_lines[] = '';
			}
			$images_api_plain = implode("\n", $img_api_lines);

			$featured_id = (int) get_post_thumbnail_id($post_id);
			$img_db_lines = array();

			if ($featured_id) {
				$img_db_lines[] = 'Featured image';
				$img_db_lines[] = 'Media ID: ' . $featured_id;
				$img_db_lines[] = 'URL: ' . (string) wp_get_attachment_url($featured_id);
				$f_src = get_post_meta($featured_id, 'trvlr_source_url', true);
				if ($f_src) {
					$img_db_lines[] = 'Source URL: ' . $f_src;
				}
				$img_db_lines[] = '';
			} else {
				$img_db_lines[] = 'Featured image: not set';
				$img_db_lines[] = '';
			}

			$gallery_count = is_array($media_ids) ? count($media_ids) : 0;
			$img_db_lines[] = 'Gallery (' . $gallery_count . ' items)';
			if (is_array($media_ids) && !empty($media_ids)) {
				foreach ($media_ids as $i => $att_id) {
					$att_id = (int) $att_id;
					$img_db_lines[] = ($i + 1) . '. Media ID: ' . $att_id;
					$img_db_lines[] = '   URL: ' . (string) wp_get_attachment_url($att_id);
					$src = get_post_meta($att_id, 'trvlr_source_url', true);
					if ($src) {
						$img_db_lines[] = '   Source URL: ' . $src;
					}
				}
			} else {
				$img_db_lines[] = '(none)';
			}

			$images_db_plain = implode("\n", $img_db_lines);
			?>
			<div class="card">
				<div class="field-header">
					<h3>Images</h3>
					<span class="badge <?php echo esc_attr($images_badge); ?>">
						<?php
						if ($images_badge === 'match') {
							echo '✓ Match';
						} else {
							echo '✗ No Match';
						}
						?>
					</span>
				</div>
				<div class="data-box">
					<div class="data-box-head">
						<div class="data-label">API — Raw (<?php echo count($api_data['images']['all_images']); ?> images)</div>
						<?php trvlr_debug_copy_btn($images_api_plain); ?>
					</div>
					<div class="data-content"><?php echo htmlspecialchars($images_api_plain); ?></div>
				</div>
				<div class="data-box">
					<div class="data-box-head">
						<div class="data-label">Database</div>
						<?php trvlr_debug_copy_btn($images_db_plain); ?>
					</div>
					<div class="data-content from-db"><?php echo htmlspecialchars($images_db_plain); ?></div>
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
			<?php if ($all_fetch_error !== ''): ?>
				<p style="color:#721c24;font-weight:600;">Fetch failed: <?php echo esc_html($all_fetch_error); ?></p>
				<?php if (!empty($all_fetch_result['raw'])): ?>
					<p style="font-size:12px;color:#666;">Non-JSON body preview:</p>
					<pre class="api-response-pre" style="max-height:220px;"><?php echo esc_html($all_fetch_result['raw']); ?></pre>
				<?php endif; ?>
			<?php elseif ($all_attractions_data !== null && !empty($all_attractions_data['results'])): ?>
				<p style="font-size:13px;color:#555;margin:0 0 10px;">
					<?php echo count($all_attractions_data['results']); ?> attractions returned (HTTP <?php echo (int) $all_fetch_result['code']; ?>)
				</p>
			<?php elseif ($all_attractions_data !== null): ?>
				<p style="font-size:13px;color:#555;margin:0 0 10px;">HTTP <?php echo (int) $all_fetch_result['code']; ?> — no <code>results</code> array in payload (see JSON below).</p>
			<?php endif; ?>
			<pre class="api-response-pre" id="api-all-json"><?php echo htmlspecialchars($raw_all_json); ?></pre>
		</div>

	</div>

	<script>
		const COPY_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>`;
		const CHECK_ICON = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;

		function copyFallback(text) {
			const ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'fixed';
			ta.style.left = '-9999px';
			ta.style.top = '0';
			document.body.appendChild(ta);
			ta.focus();
			ta.select();
			ta.setSelectionRange(0, text.length);
			let ok = false;
			try {
				ok = document.execCommand('copy');
			} catch (e) {}
			document.body.removeChild(ta);
			return ok;
		}

		function trvlrB64ToUtf8(b64) {
			const bin = atob(b64);
			const bytes = new Uint8Array(bin.length);
			for (let i = 0; i < bin.length; i++) {
				bytes[i] = bin.charCodeAt(i);
			}
			return new TextDecoder('utf-8').decode(bytes);
		}

		document.addEventListener('click', function (e) {
			const btn = e.target.closest('[data-trvlr-copy-b64]');
			if (!btn) {
				return;
			}
			e.preventDefault();
			let text;
			try {
				text = trvlrB64ToUtf8(btn.getAttribute('data-trvlr-copy-b64'));
			} catch (err) {
				return;
			}
			const label = btn.textContent;
			const done = function () {
				btn.classList.add('copied');
				btn.textContent = 'Copied';
				setTimeout(function () {
					btn.classList.remove('copied');
					btn.textContent = label;
				}, 1500);
			};
			if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
				navigator.clipboard.writeText(text).then(done).catch(function () {
					if (copyFallback(text)) {
						done();
					}
				});
			} else if (copyFallback(text)) {
				done();
			}
		});

		function copyToClipboard(text, btnId) {
			const btn = document.getElementById(btnId);
			const done = function () {
				btn.classList.add('copied');
				btn.innerHTML = CHECK_ICON + ' Copied!';
				setTimeout(function () {
					btn.classList.remove('copied');
					btn.innerHTML = COPY_ICON + ' Copy JSON';
				}, 2500);
			};
			if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
				navigator.clipboard.writeText(text).then(done).catch(function () {
					if (copyFallback(text)) {
						done();
					}
				});
			} else if (copyFallback(text)) {
				done();
			}
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
