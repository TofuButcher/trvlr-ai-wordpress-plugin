<?php
/**
 * Sync Tab - Sync Management
 * 
 * @package Trvlr
 */

if (! defined('ABSPATH')) exit;

// Get statistics
$total_attractions = wp_count_posts('trvlr_attraction')->publish;
$custom_edit_args = array(
	'post_type' => 'trvlr_attraction',
	'meta_key' => '_trvlr_has_custom_edits',
	'meta_value' => '1',
	'fields' => 'ids',
	'posts_per_page' => -1,
	'post_status' => 'any'
);
$custom_edit_posts = get_posts($custom_edit_args);
$custom_edit_count = count($custom_edit_posts);
$synced_count = $total_attractions - $custom_edit_count;
?>

<div class="trvlr-tab-content">
	<h2><?php esc_html_e('Sync Management', 'trvlr'); ?></h2>
	<p class="description"><?php esc_html_e('Manage data synchronization with the TRVLR AI system.', 'trvlr'); ?></p>

	<!-- Statistics Section -->
	<div class="trvlr-sync-stats">
		<div class="stat-box">
			<span class="stat-number"><?php echo esc_html($total_attractions); ?></span>
			<span class="stat-label">Total Attractions</span>
		</div>
		<div class="stat-box stat-box-success">
			<span class="stat-number"><?php echo esc_html($synced_count); ?></span>
			<span class="stat-label">Synced (No Edits)</span>
		</div>
		<div class="stat-box stat-box-warning">
			<span class="stat-number"><?php echo esc_html($custom_edit_count); ?></span>
			<span class="stat-label">With Custom Edits</span>
		</div>
	</div>

	<hr>

	<div class="trvlr-sync-section">
		<h3><?php esc_html_e('Manual Sync', 'trvlr'); ?></h3>
		<p><?php esc_html_e('Manually trigger a sync with the TRVLR AI system.', 'trvlr'); ?></p>
		<p>
			<button id="trvlr-sync-now" class="button button-primary">
				<?php esc_html_e('Sync Now', 'trvlr'); ?>
			</button>
			<span id="trvlr-sync-status" style="margin-left: 10px;"></span>
		</p>
	</div>

	<hr>

	<div class="trvlr-sync-section">
		<h3><?php esc_html_e('Scheduled Sync', 'trvlr'); ?></h3>
		<p><?php esc_html_e('Configure automatic synchronization schedule.', 'trvlr'); ?></p>

		<?php
		$sync_enabled = Trvlr_Scheduler::is_sync_enabled();
		$sync_frequency = Trvlr_Scheduler::get_sync_frequency();
		$next_sync = Trvlr_Scheduler::get_next_sync_time();
		?>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Automatic Sync', 'trvlr'); ?></th>
				<td>
					<label>
						<input type="checkbox" id="trvlr-sync-enabled" <?php checked($sync_enabled, true); ?>>
						<?php esc_html_e('Enable automatic synchronization', 'trvlr'); ?>
					</label>
					<?php if ($sync_enabled && $next_sync) : ?>
						<p class="description">
							<?php
							printf(
								__('Next sync scheduled for: %s', 'trvlr'),
								'<strong>' . date('Y-m-d H:i:s', $next_sync) . '</strong>'
							);
							?>
						</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Sync Frequency', 'trvlr'); ?></th>
				<td>
					<select id="trvlr-sync-frequency" <?php disabled(!$sync_enabled); ?>>
						<option value="hourly" <?php selected($sync_frequency, 'hourly'); ?>><?php esc_html_e('Hourly', 'trvlr'); ?></option>
						<option value="twicedaily" <?php selected($sync_frequency, 'twicedaily'); ?>><?php esc_html_e('Twice Daily', 'trvlr'); ?></option>
						<option value="daily" <?php selected($sync_frequency, 'daily'); ?>><?php esc_html_e('Daily', 'trvlr'); ?></option>
						<option value="weekly" <?php selected($sync_frequency, 'weekly'); ?>><?php esc_html_e('Weekly', 'trvlr'); ?></option>
					</select>
					<p class="description"><?php esc_html_e('How often should attractions be synced automatically?', 'trvlr'); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="button" id="trvlr-save-schedule" class="button button-primary">
				<?php esc_html_e('Save Schedule Settings', 'trvlr'); ?>
			</button>
			<span id="trvlr-schedule-status" style="margin-left: 10px;"></span>
		</p>
	</div>

	<hr>

	<!-- Custom Edits Management -->
	<?php if ($custom_edit_count > 0) : ?>
		<div class="trvlr-sync-section">
			<h3><?php esc_html_e('Attractions with Custom Edits', 'trvlr'); ?></h3>
			<p class="description"><?php esc_html_e('These attractions have been manually edited in WordPress. Expand each row to select which fields to overwrite with TRVLR data on the next sync.', 'trvlr'); ?></p>

			<form id="trvlr-force-sync-form">
				<table class="wp-list-table widefat fixed striped trvlr-custom-edits-table">
					<thead>
						<tr>
							<th style="width: 40%;"><?php esc_html_e('Attraction', 'trvlr'); ?></th>
							<th style="width: 25%;"><?php esc_html_e('Edited Fields', 'trvlr'); ?></th>
							<th style="width: 15%;"><?php esc_html_e('Last Modified', 'trvlr'); ?></th>
							<th style="width: 20%;"><?php esc_html_e('Force Sync', 'trvlr'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Get full post objects for display
						$custom_edit_posts_full = get_posts(array(
							'post_type' => 'trvlr_attraction',
							'meta_key' => '_trvlr_has_custom_edits',
							'meta_value' => '1',
							'posts_per_page' => -1,
							'post_status' => 'any',
							'orderby' => 'modified',
							'order' => 'DESC'
						));

						foreach ($custom_edit_posts_full as $post) :
							$edited_fields = get_post_meta($post->ID, '_trvlr_edited_fields', true);
							$force_sync_fields = get_post_meta($post->ID, '_trvlr_force_sync_fields', true);
							if (!is_array($force_sync_fields)) {
								$force_sync_fields = array();
							}
							if (!is_array($edited_fields)) {
								$edited_fields = array();
							}
							
							// Get human-readable field names from centralized map
							$field_labels = Trvlr_Field_Map::get_field_labels();
							
							$force_sync_labels = array();
							foreach ($force_sync_fields as $field) {
								if (isset($field_labels[$field])) {
									$force_sync_labels[] = $field_labels[$field];
								}
							}
						?>
							<tr class="trvlr-edits-row" data-post-id="<?php echo esc_attr($post->ID); ?>">
								<td>
									<strong><a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html(get_the_title($post->ID)); ?></a></strong>
								</td>
								<td>
									<span class="edited-fields-badge">
										<?php 
										$field_display = array();
										foreach ($edited_fields as $field) {
											if (isset($field_labels[$field])) {
												$field_display[] = $field_labels[$field];
											}
										}
										echo esc_html(implode(', ', $field_display)); 
										?>
									</span>
								</td>
								<td><?php echo esc_html(get_the_modified_date('Y-m-d H:i', $post->ID)); ?></td>
								<td>
									<?php if (!empty($force_sync_fields)) : ?>
										<span class="force-sync-status">
											<span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
											<strong><?php esc_html_e('Will overwrite:', 'trvlr'); ?></strong> <?php echo esc_html(implode(', ', $force_sync_labels)); ?>
										</span>
										<br>
									<?php endif; ?>
									<button type="button" class="button button-small trvlr-toggle-fields" data-post-id="<?php echo esc_attr($post->ID); ?>">
										<span class="dashicons dashicons-arrow-down-alt2"></span>
										<?php esc_html_e('Select Fields', 'trvlr'); ?>
									</button>
								</td>
							</tr>
							<tr class="trvlr-field-selection-row" id="trvlr-fields-<?php echo esc_attr($post->ID); ?>" style="display: none;">
								<td colspan="4" style="background: #f9f9f9; padding: 15px;">
									<div class="trvlr-field-checkboxes">
										<label style="font-weight: bold; margin-right: 20px;">
											<input type="checkbox" class="trvlr-select-all-fields" data-post-id="<?php echo esc_attr($post->ID); ?>">
											<?php esc_html_e('Select All Fields', 'trvlr'); ?>
										</label>
										<br><br>
										<?php foreach ($edited_fields as $field) : ?>
											<label style="display: inline-block; margin-right: 20px; margin-bottom: 8px;">
												<input 
													type="checkbox" 
													name="force_sync_fields[<?php echo esc_attr($post->ID); ?>][]" 
													value="<?php echo esc_attr($field); ?>"
													<?php checked(in_array($field, $force_sync_fields)); ?>
													class="trvlr-field-checkbox"
													data-post-id="<?php echo esc_attr($post->ID); ?>"
												>
												<?php echo esc_html(isset($field_labels[$field]) ? $field_labels[$field] : $field); ?>
											</label>
										<?php endforeach; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<p class="submit">
					<button type="button" id="trvlr-save-force-sync" class="button button-primary">
						<?php esc_html_e('Save Force Sync Settings', 'trvlr'); ?>
					</button>
					<button type="button" id="trvlr-clear-all-edits" class="button">
						<?php esc_html_e('Clear All Force Sync Settings', 'trvlr'); ?>
					</button>
					<span id="trvlr-force-sync-status" style="margin-left: 10px;"></span>
				</p>
			</form>
		</div>

		<hr>
	<?php endif; ?>

	<div class="trvlr-sync-section trvlr-danger-zone">
		<h3 style="color: #d63638;"><?php esc_html_e('Danger Zone', 'trvlr'); ?></h3>
		<p><?php esc_html_e('Delete data imported by this plugin.', 'trvlr'); ?></p>
		<p>
			<button id="trvlr-delete-all" class="button button-link-delete" style="margin-right: 15px;">
				<?php esc_html_e('Delete EVERYTHING (Inc. Images)', 'trvlr'); ?>
			</button>
			<button id="trvlr-delete-posts" class="button button-secondary">
				<?php esc_html_e('Delete Posts Only (Keep Images)', 'trvlr'); ?>
			</button>
			<span id="trvlr-delete-status" style="margin-left: 10px;"></span>
		</p>
	</div>
</div>
