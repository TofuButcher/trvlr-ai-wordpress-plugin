<?php
include_once plugin_dir_path(__FILE__) . 'trvlr-admin-header.php';
?>

<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<form method="post" action="options.php">
	<?php
	settings_fields('trvlr_settings_group');
	do_settings_sections('trvlr_settings_group');
	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e('Organisation ID', 'trvlr'); ?></th>
			<td>
				<input type="text" name="trvlr_organisation_id" value="<?php echo esc_attr(get_option('trvlr_organisation_id')); ?>" />
				<p class="description"><?php esc_html_e('Your Organisation ID from TRVLR AI.', 'trvlr'); ?></p>
			</td>
		</tr>

		<!-- Placeholder for API Key if needed later -->
		<tr valign="top">
			<th scope="row"><?php esc_html_e('API Key', 'trvlr'); ?></th>
			<td>
				<input type="text" name="trvlr_api_key" value="<?php echo esc_attr(get_option('trvlr_api_key')); ?>" class="regular-text" />
				<p class="description"><?php esc_html_e('API Key for authentication (if required).', 'trvlr'); ?></p>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>

<hr>

<h2><?php esc_html_e('Data Management', 'trvlr'); ?></h2>
<p><?php esc_html_e('Tools for managing synchronized data.', 'trvlr'); ?></p>

<div class="card">
	<h3><?php esc_html_e('Manual Sync', 'trvlr'); ?></h3>
	<p><?php esc_html_e('Manually trigger a sync with the TRVLR AI system.', 'trvlr'); ?></p>
	<p>
		<button id="trvlr-sync-now" class="button button-secondary"><?php esc_html_e('Sync Now', 'trvlr'); ?></button>
		<span id="trvlr-sync-status" style="margin-left: 10px;"></span>
	</p>
</div>

<div class="card" style="border-color: #d63638;">
	<h3 style="color: #d63638;"><?php esc_html_e('Danger Zone', 'trvlr'); ?></h3>
	<p><?php esc_html_e('Delete data imported by this plugin.', 'trvlr'); ?></p>
	<p>
		<button id="trvlr-delete-all" class="button button-link-delete" style="margin-right: 15px;"><?php esc_html_e('Delete EVERYTHING (Inc. Images)', 'trvlr'); ?></button>
		<button id="trvlr-delete-posts" class="button button-secondary"><?php esc_html_e('Delete Posts Only (Keep Images)', 'trvlr'); ?></button>
		<span id="trvlr-delete-status" style="margin-left: 10px;"></span>
	</p>
</div>
<?php
include_once plugin_dir_path(__FILE__) . 'trvlr-admin-footer.php';
?>