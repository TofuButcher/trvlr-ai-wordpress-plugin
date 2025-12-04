<?php
/**
 * Email Notification Settings
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin/partials
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}

$notification_email = get_option('trvlr_notification_email', get_option('admin_email'));
$enabled_notifications = get_option('trvlr_enabled_notifications', array());
$notification_types = Trvlr_Notifier::get_notification_types();
?>

<div class="trvlr-settings-section">
	<h2><?php esc_html_e('Email Notifications', 'trvlr'); ?></h2>
	<p><?php esc_html_e('Configure email notifications for sync events and errors.', 'trvlr'); ?></p>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="trvlr-notification-email"><?php esc_html_e('Notification Email', 'trvlr'); ?></label>
			</th>
			<td>
				<input 
					type="email" 
					id="trvlr-notification-email" 
					name="trvlr_notification_email" 
					value="<?php echo esc_attr($notification_email); ?>" 
					class="regular-text"
				>
				<p class="description">
					<?php esc_html_e('Email address to receive notifications. Defaults to site admin email.', 'trvlr'); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php esc_html_e('Enable Notifications', 'trvlr'); ?></th>
			<td>
				<fieldset>
					<?php foreach ($notification_types as $type_key => $type_label) : ?>
						<label>
							<input 
								type="checkbox" 
								name="trvlr_enabled_notifications[]" 
								value="<?php echo esc_attr($type_key); ?>"
								<?php checked(in_array($type_key, $enabled_notifications)); ?>
							>
							<?php echo esc_html($type_label); ?>
						</label>
						<br>
					<?php endforeach; ?>
				</fieldset>
			</td>
		</tr>
	</table>

	<p class="submit">
		<button type="button" id="trvlr-save-notifications" class="button button-primary">
			<?php esc_html_e('Save Notification Settings', 'trvlr'); ?>
		</button>
		<span id="trvlr-notifications-status" style="margin-left: 10px;"></span>
	</p>

	<hr>

	<h3><?php esc_html_e('Test Notifications', 'trvlr'); ?></h3>
	<p><?php esc_html_e('Send a test email to verify your notification settings.', 'trvlr'); ?></p>
	<p>
		<button type="button" id="trvlr-send-test-email" class="button">
			<?php esc_html_e('Send Test Email', 'trvlr'); ?>
		</button>
		<span id="trvlr-test-email-status" style="margin-left: 10px;"></span>
	</p>
</div>

