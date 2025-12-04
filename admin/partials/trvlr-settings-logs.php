<?php

/**
 * Logs Tab - Recent Activity
 * 
 * @package Trvlr
 */

if (! defined('ABSPATH')) exit;

$logs = Trvlr_Logger::get_logs(100);
?>

<div class="trvlr-tab-content">
    <h2><?php esc_html_e('Recent Logs', 'trvlr'); ?></h2>
    <p class="description"><?php esc_html_e('View recent synchronization and system activity.', 'trvlr'); ?></p>

    <!-- Export Section -->
    <div class="trvlr-logs-export" style="margin-bottom: 30px; background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
        <h3 style="margin-top: 0;"><?php esc_html_e('Export Logs', 'trvlr'); ?></h3>
        <p><?php esc_html_e('Download log data as CSV for archiving or analysis.', 'trvlr'); ?></p>

        <div class="trvlr-export-filters" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="export-type-filter"><strong><?php esc_html_e('Log Type:', 'trvlr'); ?></strong></label><br>
                <select id="export-type-filter" style="width: 100%; margin-top: 5px;">
                    <option value=""><?php esc_html_e('All Types', 'trvlr'); ?></option>
                    <option value="sync_start"><?php esc_html_e('Sync Started', 'trvlr'); ?></option>
                    <option value="sync_complete"><?php esc_html_e('Sync Completed', 'trvlr'); ?></option>
                    <option value="attraction_created"><?php esc_html_e('Created', 'trvlr'); ?></option>
                    <option value="attraction_updated"><?php esc_html_e('Updated', 'trvlr'); ?></option>
                    <option value="attraction_skipped"><?php esc_html_e('Skipped', 'trvlr'); ?></option>
                    <option value="error"><?php esc_html_e('Errors', 'trvlr'); ?></option>
                    <option value="system"><?php esc_html_e('System', 'trvlr'); ?></option>
                    <option value="notification"><?php esc_html_e('Notification', 'trvlr'); ?></option>
                </select>
            </div>
            <div>
                <label for="export-date-from"><strong><?php esc_html_e('From Date:', 'trvlr'); ?></strong></label><br>
                <input type="date" id="export-date-from" style="width: 100%; margin-top: 5px;">
            </div>
            <div>
                <label for="export-date-to"><strong><?php esc_html_e('To Date:', 'trvlr'); ?></strong></label><br>
                <input type="date" id="export-date-to" style="width: 100%; margin-top: 5px;">
            </div>
            <div>
                <label for="export-limit"><strong><?php esc_html_e('Limit:', 'trvlr'); ?></strong></label><br>
                <input type="number" id="export-limit" placeholder="All" min="1" style="width: 100%; margin-top: 5px;">
            </div>
        </div>
        <p>
            <button id="trvlr-export-logs" class="button button-primary">
                <span class="dashicons dashicons-download" style="vertical-align: middle; margin-top: -2px;"></span>
                <?php esc_html_e('Download CSV', 'trvlr'); ?>
            </button>
            <span id="trvlr-export-status" style="margin-left: 10px;"></span>
        </p>
    </div>

    <div class="trvlr-logs-header">
        <div class="trvlr-logs-filters">
            <label for="log-type-filter"><?php esc_html_e('Filter by type:', 'trvlr'); ?></label>
            <select id="log-type-filter">
                <option value=""><?php esc_html_e('All Types', 'trvlr'); ?></option>
                <option value="sync_start"><?php esc_html_e('Sync Started', 'trvlr'); ?></option>
                <option value="sync_complete"><?php esc_html_e('Sync Completed', 'trvlr'); ?></option>
                <option value="attraction_created"><?php esc_html_e('Created', 'trvlr'); ?></option>
                <option value="attraction_updated"><?php esc_html_e('Updated', 'trvlr'); ?></option>
                <option value="attraction_skipped"><?php esc_html_e('Skipped', 'trvlr'); ?></option>
                <option value="error"><?php esc_html_e('Errors', 'trvlr'); ?></option>
            </select>
        </div>
        <div class="trvlr-logs-actions">
            <button class="button" id="trvlr-clear-old-logs"><?php esc_html_e('Clear Logs Older Than 30 Days', 'trvlr'); ?></button>
            <button class="button button-secondary" id="trvlr-clear-all-logs"><?php esc_html_e('Clear All Logs', 'trvlr'); ?></button>
            <span id="trvlr-logs-status" style="margin-left: 10px;"></span>
        </div>
    </div>

    <?php if (empty($logs)) : ?>
        <div class="notice notice-info inline">
            <p><?php esc_html_e('No logs found. Logs will appear here after syncing attractions.', 'trvlr'); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped trvlr-logs-table">
            <thead>
                <tr>
                    <th style="width: 150px;"><?php esc_html_e('Time', 'trvlr'); ?></th>
                    <th style="width: 140px;"><?php esc_html_e('Type', 'trvlr'); ?></th>
                    <th><?php esc_html_e('Message', 'trvlr'); ?></th>
                    <th style="width: 80px;"><?php esc_html_e('Details', 'trvlr'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) :
                    $log_class = 'log-' . esc_attr($log->log_type);
                ?>
                    <tr class="<?php echo esc_attr($log_class); ?>" data-log-type="<?php echo esc_attr($log->log_type); ?>">
                        <td><?php echo esc_html(date('Y-m-d H:i:s', strtotime($log->created_at))); ?></td>
                        <td>
                            <span class="log-badge log-badge-<?php echo esc_attr($log->log_type); ?>">
                                <?php echo esc_html(str_replace('_', ' ', ucwords($log->log_type, '_'))); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($log->message); ?></td>
                        <td>
                            <?php if ($log->details && $log->details !== '[]' && $log->details !== 'null') : ?>
                                <button class="button button-small trvlr-view-log-details" data-details="<?php echo esc_attr($log->details); ?>">
                                    <?php esc_html_e('View', 'trvlr'); ?>
                                </button>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal for viewing log details -->
<div id="trvlr-log-details-modal" class="trvlr-modal" style="display: none;">
    <div class="trvlr-modal-content">
        <span class="trvlr-modal-close">&times;</span>
        <h3><?php esc_html_e('Log Details', 'trvlr'); ?></h3>
        <pre id="trvlr-log-details-content"></pre>
    </div>
</div>