<?php

/**
 * Attraction meta boxes and field UI driven by Trvlr_Field_Map.
 *
 * @package Trvlr
 */

require_once plugin_dir_path(__FILE__) . 'class-trvlr-meta-repeater.php';

/**
 * Repeater instances built from the field registry.
 *
 * @return array<string, Trvlr_Meta_Repeater>
 */
function trvlr_get_repeater_instances()
{
    $instances = array();
    foreach (Trvlr_Field_Map::get_meta_ui_fields() as $field_name => $config) {
        if (empty($config['ui']['control']) || $config['ui']['control'] !== 'repeater') {
            continue;
        }
        $columns = isset($config['ui']['repeater']) ? $config['ui']['repeater'] : array();
        $instances[$field_name] = new Trvlr_Meta_Repeater(
            'trvlr_attraction',
            $field_name,
            $config['label'],
            $columns
        );
    }
    return $instances;
}

/**
 * @return void
 */
function trvlr_register_meta_boxes()
{
    add_meta_box(
        'trvlr_sync_actions',
        'TRVLR Sync',
        'trvlr_render_sync_actions_meta_box',
        'trvlr_attraction',
        'side',
        'high'
    );

    add_meta_box(
        'trvlr_attraction_details',
        'Attraction Details',
        'trvlr_render_details_meta_box',
        'trvlr_attraction',
        'normal',
        'core'
    );

    remove_meta_box('postcustom', 'trvlr_attraction', 'normal');
}
add_action('add_meta_boxes', 'trvlr_register_meta_boxes');

/**
 * Whether this attraction uses Synced / Custom Edit chrome at all.
 *
 * @param int $post_id
 * @return bool
 */
function trvlr_field_sync_enabled($post_id)
{
    return (bool) get_post_meta($post_id, 'trvlr_id', true);
}

/**
 * Whether a field should show Synced / Custom Edit chrome.
 *
 * @param int    $post_id
 * @param string $field
 * @return bool
 */
function trvlr_field_uses_sync_chrome($post_id, $field)
{
    return trvlr_field_sync_enabled($post_id) && Trvlr_Field_Map::is_syncable($field);
}

/**
 * Open Synced / Custom Edit field wrapper markup.
 *
 * @param int    $post_id
 * @param string $field
 * @param string $label
 * @return void
 */
function trvlr_field_sync_open($post_id, $field, $label = '')
{
    $GLOBALS['trvlr_field_sync_wrap_open'] = false;

    if (!trvlr_field_uses_sync_chrome($post_id, $field)) {
        return;
    }

    $is_custom = trvlr_is_custom_edit($post_id, $field);
    $mode_class = $is_custom ? 'is-custom-edit' : 'is-synced';
    $badge = $is_custom ? __('Not Synced - WP Edit', 'trvlr') : __('Synced to Traveloris', 'trvlr');
    $button = $is_custom ? __('Enable Traveloris Sync', 'trvlr') : __('Custom Edit', 'trvlr');

    echo '<div class="trvlr-field-sync ' . esc_attr($mode_class) . '" data-field="' . esc_attr($field) . '">';
    echo '<div class="trvlr-field-sync-bar">';
    if ($label !== '') {
        echo '<span class="trvlr-field-sync-label">' . esc_html($label) . '</span>';
    }
    echo '<span class="trvlr-field-mode-badge">' . esc_html($badge) . '</span>';
    echo '<button type="button" class="button-link trvlr-field-mode-toggle">' . esc_html($button) . '</button>';
    echo '</div>';
    echo '<div class="trvlr-field-sync-body">';
    $GLOBALS['trvlr_field_sync_wrap_open'] = true;
}

/**
 * Close Synced / Custom Edit field wrapper markup.
 *
 * @return void
 */
function trvlr_field_sync_close()
{
    if (empty($GLOBALS['trvlr_field_sync_wrap_open'])) {
        return;
    }
    echo '</div></div>';
    $GLOBALS['trvlr_field_sync_wrap_open'] = false;
}

/**
 * @param WP_Post $post
 * @return void
 */
function trvlr_render_sync_actions_meta_box($post)
{
    $trvlr_id = get_post_meta($post->ID, 'trvlr_id', true);
    $edited_fields = trvlr_get_custom_edit_fields($post->ID);
    $field_labels = Trvlr_Field_Map::get_field_labels();

?>
    <div id="trvlr-sync-actions" style="padding: 10px 0;">
        <?php if ($trvlr_id): ?>
            <p style="margin: 0 0 10px 0; font-size: 12px; color: #666;">
                <strong>TRVLR ID:</strong> <?php echo esc_html($trvlr_id); ?>
            </p>

            <div id="trvlr-custom-edits-sidebar" style="background: #fff3cd; border: 1px solid #ffc107; padding: 8px; margin-bottom: 10px; border-radius: 3px; font-size: 12px;<?php echo empty($edited_fields) ? ' display:none;' : ''; ?>">
                <strong><?php esc_html_e('Custom Edit fields', 'trvlr'); ?></strong>
                <ul class="trvlr-custom-edits-list" id="trvlr-custom-edits-list">
                    <?php foreach ($edited_fields as $field): ?>
                        <li data-field="<?php echo esc_attr($field); ?>">
                            <?php echo esc_html(isset($field_labels[$field]) ? $field_labels[$field] : $field); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p style="margin: 6px 0 0; color: #666;"><?php esc_html_e('These stay in WordPress until you enable Traveloris sync on each field.', 'trvlr'); ?></p>
            </div>

            <button type="button" id="trvlr-sync-single-btn" class="button button-primary" style="width: 100%;">
                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                Sync from TRVLR
            </button>

            <div id="trvlr-sync-message" style="margin-top: 10px; padding: 8px; border-radius: 3px; display: none;"></div>
        <?php else: ?>
            <p style="color: #999; font-size: 12px; margin: 0;">
                This attraction is not synced with TRVLR.
            </p>
        <?php endif; ?>
    </div>

    <style>
        #trvlr-sync-single-btn.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        #trvlr-sync-single-btn.loading .dashicons {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #trvlr-sync-message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        #trvlr-sync-message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            window.trvlrUpdateCustomEditsSidebar = function(fields, labels) {
                var $box = $('#trvlr-custom-edits-sidebar');
                var $list = $('#trvlr-custom-edits-list');
                $list.empty();
                if (!fields || !fields.length) {
                    $box.hide();
                    return;
                }
                fields.forEach(function(field) {
                    var label = (labels && labels[field]) ? labels[field] : field;
                    $list.append($('<li/>').attr('data-field', field).text(label));
                });
                $box.show();
            };

            $('#trvlr-sync-single-btn').on('click', function() {
                var $btn = $(this);
                var $message = $('#trvlr-sync-message');
                var postId = <?php echo absint($post->ID); ?>;

                $btn.addClass('loading');
                $message.hide().removeClass('success error');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'trvlr_sync_single',
                        post_id: postId,
                        nonce: '<?php echo wp_create_nonce('trvlr_sync_single'); ?>'
                    },
                    success: function(response) {
                        $btn.removeClass('loading');

                        if (response.success) {
                            $message
                                .addClass('success')
                                .html('<strong>✓ ' + response.data.message + '</strong>')
                                .show();

                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $message
                                .addClass('error')
                                .html('<strong>✗ Error:</strong> ' + (response.data.message || 'Unknown error'))
                                .show();
                        }
                    },
                    error: function() {
                        $btn.removeClass('loading');
                        $message
                            .addClass('error')
                            .html('<strong>✗ Connection error.</strong> Please try again.')
                            .show();
                    }
                });
            });
        });
    </script>
<?php
}

/**
 * @return void
 */
function trvlr_move_editor_below_metabox()
{
    if (get_post_type() === 'trvlr_attraction') {
        remove_post_type_support('trvlr_attraction', 'editor');
    }
}
add_action('admin_head', 'trvlr_move_editor_below_metabox');

/**
 * @return void
 */
function trvlr_add_editor_after_metabox()
{
    if (get_post_type() === 'trvlr_attraction') {
        add_meta_box(
            'trvlr_content_editor',
            'Content',
            'trvlr_render_editor_meta_box',
            'trvlr_attraction',
            'normal',
            'low'
        );
    }
}
add_action('add_meta_boxes', 'trvlr_add_editor_after_metabox', 20);

/**
 * @param WP_Post $post
 * @return void
 */
function trvlr_render_editor_meta_box($post)
{
    wp_editor($post->post_content, 'content', array(
        'textarea_rows' => 15,
        'teeny' => false,
        'media_buttons' => true
    ));
}

/**
 * Render a single meta UI field from the registry.
 *
 * @param int    $post_id
 * @param string $field_name
 * @param array  $config Field map entry with `label` and `ui`.
 * @return void
 */
function trvlr_render_meta_ui_field($post_id, $field_name, $config)
{
    $ui = $config['ui'];
    $label = $config['label'];
    $control = isset($ui['control']) ? $ui['control'] : 'text';
    $span = isset($ui['span']) ? max(1, min(12, (int) $ui['span'])) : 12;
    $value = Trvlr_Field_Map::get_field_value($post_id, $field_name);
    $uses_chrome = trvlr_field_uses_sync_chrome($post_id, $field_name);

    echo '<div class="trvlr-meta-field trvlr-meta-span-' . esc_attr((string) $span) . '">';
    trvlr_field_sync_open($post_id, $field_name, $label);

    switch ($control) {
        case 'richtext':
            echo '<div class="trvlr-row">';
            if (!$uses_chrome) {
                echo '<label>' . esc_html($label) . '</label>';
            }
            $editor_args = array(
                'media_buttons' => false,
                'textarea_rows' => isset($ui['rows']) ? (int) $ui['rows'] : 5,
            );
            if (!empty($ui['teeny'])) {
                $editor_args['teeny'] = true;
            }
            wp_editor(is_string($value) ? $value : '', $field_name, $editor_args);
            echo '</div>';
            break;

        case 'checkbox':
            echo '<div class="trvlr-row">';
            echo '<label class="trvlr-field-control-label">';
            echo '<input type="checkbox" name="' . esc_attr($field_name) . '" value="1" ' . checked($value, '1', false) . '>';
            if (!$uses_chrome) {
                echo ' ' . esc_html($label);
            }
            echo '</label>';
            echo '</div>';
            break;

        case 'time':
            echo '<div class="trvlr-row">';
            if (!$uses_chrome) {
                echo '<label>' . esc_html($label) . '</label>';
            }
            echo '<input type="time" name="' . esc_attr($field_name) . '" value="' . esc_attr(is_string($value) ? $value : '') . '" class="regular-text">';
            echo '</div>';
            break;

        case 'gallery':
            $media_ids = is_array($value) ? $value : array();
            echo '<div class="trvlr-row">';
            if (!$uses_chrome) {
                echo '<label>' . esc_html($label) . '</label>';
            }
            echo '<div class="trvlr-media-gallery" id="trvlr-media-gallery">';
            foreach ($media_ids as $attachment_id) {
                $img_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                if (!$img_url) {
                    continue;
                }
                echo '<div class="media-item" data-id="' . esc_attr($attachment_id) . '">';
                echo '<img src="' . esc_url($img_url) . '" alt="" />';
                echo '<button type="button" class="remove" onclick="trvlrRemoveMedia(this)" aria-label="Remove media">';
                echo '<span class="dashicons dashicons-trash" aria-hidden="true"></span>';
                echo '</button>';
                echo '<input type="hidden" name="trvlr_media[]" value="' . esc_attr($attachment_id) . '">';
                echo '</div>';
            }
            echo '</div>';
            echo '<button type="button" class="button trvlr-add-media" onclick="trvlrOpenMediaUploader(event)">Add Media</button>';
            echo '</div>';
            break;

        case 'repeater':
            $repeaters = trvlr_get_repeater_instances();
            if (isset($repeaters[$field_name])) {
                $repeaters[$field_name]->render($post_id);
            }
            break;

        case 'text':
        default:
            echo '<div class="trvlr-row">';
            if (!$uses_chrome) {
                echo '<label>' . esc_html($label) . '</label>';
            }
            echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr(is_string($value) ? $value : '') . '" class="widefat">';
            if (!empty($ui['description'])) {
                echo '<span class="description">' . esc_html($ui['description']) . '</span>';
            }
            echo '</div>';
            break;
    }

    trvlr_field_sync_close();
    echo '</div>';
}

/**
 * Render Attraction Details from the field registry.
 *
 * @param WP_Post $post
 * @return void
 */
function trvlr_render_details_meta_box($post)
{
    wp_nonce_field('trvlr_save_details', 'trvlr_details_nonce');
    $trvlr_id = get_post_meta($post->ID, 'trvlr_id', true);

?>
    <div class="trvlr-meta-fields-grid">
        <div class="trvlr-meta-readonly trvlr-row">
            <label>TRVLR ID</label>
            <input type="text" class="regular-text" value="<?php echo esc_attr($trvlr_id); ?>" readonly disabled>
            <span class="description">System ID (Read Only)</span>
        </div>

        <?php foreach (Trvlr_Field_Map::get_meta_ui_fields() as $field_name => $config): ?>
            <?php trvlr_render_meta_ui_field($post->ID, $field_name, $config); ?>
        <?php endforeach; ?>
    </div>

    <script>
        function trvlrOpenMediaUploader(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Select or Upload Media',
                button: {
                    text: 'Add to Gallery'
                },
                multiple: true
            });

            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var gallery = document.getElementById('trvlr-media-gallery');

                selection.forEach(function(attachment) {
                    attachment = attachment.toJSON();
                    var thumb = attachment.sizes && attachment.sizes.thumbnail
                        ? attachment.sizes.thumbnail.url
                        : attachment.url;
                    var itemHtml = '<div class="media-item" data-id="' + attachment.id + '">' +
                        '<img src="' + thumb + '" alt="" />' +
                        '<button type="button" class="remove" onclick="trvlrRemoveMedia(this)" aria-label="Remove media">' +
                        '<span class="dashicons dashicons-trash" aria-hidden="true"></span>' +
                        '</button>' +
                        '<input type="hidden" name="trvlr_media[]" value="' + attachment.id + '">' +
                        '</div>';
                    gallery.insertAdjacentHTML('beforeend', itemHtml);
                });
            });

            frame.open();
        }

        function trvlrRemoveMedia(btn) {
            if (confirm('Remove this media item?')) {
                btn.closest('.media-item').remove();
            }
        }
    </script>
<?php
}

/**
 * Whether a syncable field may accept an admin save write.
 *
 * @param int    $post_id
 * @param string $field_name
 * @return bool
 */
function trvlr_field_allows_admin_save($post_id, $field_name)
{
    if (!Trvlr_Field_Map::is_syncable($field_name)) {
        return true;
    }
    if (!trvlr_field_sync_enabled($post_id)) {
        return true;
    }
    return trvlr_is_custom_edit($post_id, $field_name);
}

/**
 * Save attraction detail meta from the field registry.
 *
 * @param int $post_id
 * @return void
 */
function trvlr_save_details_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'trvlr_attraction') {
        return;
    }
    if (defined('TRVLR_SYNCING') && TRVLR_SYNCING) {
        return;
    }
    if (!isset($_POST['trvlr_details_nonce']) || !wp_verify_nonce($_POST['trvlr_details_nonce'], 'trvlr_save_details')) {
        return;
    }

    $repeaters = trvlr_get_repeater_instances();

    foreach (Trvlr_Field_Map::get_meta_ui_fields() as $field_name => $config) {
        if (!trvlr_field_allows_admin_save($post_id, $field_name)) {
            continue;
        }

        $control = isset($config['ui']['control']) ? $config['ui']['control'] : 'text';

        switch ($control) {
            case 'richtext':
                if (isset($_POST[$field_name])) {
                    update_post_meta($post_id, $field_name, wp_kses_post(wp_unslash($_POST[$field_name])));
                }
                break;

            case 'checkbox':
                update_post_meta($post_id, $field_name, isset($_POST[$field_name]) ? '1' : '0');
                break;

            case 'gallery':
                if (isset($_POST['trvlr_media'])) {
                    if (is_array($_POST['trvlr_media']) && !empty($_POST['trvlr_media'])) {
                        update_post_meta($post_id, $field_name, array_map('intval', $_POST['trvlr_media']));
                    } else {
                        delete_post_meta($post_id, $field_name);
                    }
                }
                break;

            case 'repeater':
                if (isset($repeaters[$field_name])) {
                    $repeaters[$field_name]->save($post_id);
                }
                break;

            case 'time':
            case 'text':
            default:
                if (isset($_POST[$field_name])) {
                    update_post_meta($post_id, $field_name, sanitize_text_field(wp_unslash($_POST[$field_name])));
                }
                break;
        }
    }
}
add_action('save_post', 'trvlr_save_details_meta');
