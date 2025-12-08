<?php

/**
 * Sync Engine - Handles syncing attractions from TRVLR API
 *
 * @package    Trvlr
 * @subpackage Trvlr/core
 */

class Trvlr_Sync
{
    public function sync_all()
    {
        // Generate unique session ID for this sync
        $session_id = 'sync_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 8);
        $GLOBALS['trvlr_current_sync_session'] = $session_id;

        Trvlr_Logger::log('sync_start', 'Sync initiated', array('user_id' => get_current_user_id()), $session_id);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        // Load mock data files
        $multiple_file = plugin_dir_path(dirname(__FILE__)) . 'api/multiple-attractions.json';
        $single_file = plugin_dir_path(dirname(__FILE__)) . 'api/single-attraction.json';

        if (!file_exists($multiple_file) || !file_exists($single_file)) {
            Trvlr_Logger::log('error', 'Mock files missing');
            return;
        }

        $list_data = json_decode(file_get_contents($multiple_file), true);
        $single_data_wrapper = json_decode(file_get_contents($single_file), true);
        $single_data = !empty($single_data_wrapper['results'][0]) ? $single_data_wrapper['results'][0] : array();

        if (empty($list_data['results'])) {
            Trvlr_Logger::log('error', 'No attractions found in API response');
            return;
        }

        foreach ($list_data['results'] as $list_item) {
            $attraction_data = array();

            $attraction_data['pk'] = isset($list_item['pk']) ? $list_item['pk'] : 0;
            $attraction_data['id'] = $attraction_data['pk'];
            $attraction_data['title'] = isset($list_item['title']) ? $list_item['title'] : '';

            if (isset($list_item['images']) && is_string($list_item['images'])) {
                $attraction_data['images'] = array(
                    'all_images' => array(array('url' => $list_item['images']))
                );
            }

            foreach ($single_data as $key => $val) {
                if (!isset($attraction_data[$key]) && !in_array($key, array('id', 'pk', 'title', 'images'))) {
                    $attraction_data[$key] = $val;
                }
            }

            if ($attraction_data['pk'] == 5220) {
                $attraction_data = $single_data;
            }

            $result = $this->update_attraction_post($attraction_data);

            if ($result === 'created') $created++;
            elseif ($result === 'updated') $updated++;
            elseif ($result === 'skipped') $skipped++;
            elseif ($result === 'error') $errors++;
        }

        $message = "Sync completed: {$created} created, {$updated} updated, {$skipped} skipped" . ($errors > 0 ? ", {$errors} errors" : "");
        Trvlr_Logger::log('sync_complete', $message, array(
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ), $session_id);

        Trvlr_Notifier::notify_sync_complete($created, $updated, $skipped, $errors);

        // Clean up session global
        unset($GLOBALS['trvlr_current_sync_session']);
    }

    private function update_attraction_post($data)
    {
        $attraction_id = isset($data['pk']) ? $data['pk'] : (isset($data['id']) ? $data['id'] : 0);

        if (!$attraction_id) {
            Trvlr_Logger::log('error', 'Missing attraction ID');
            return 'error';
        }

        $existing_post = $this->get_post_by_trvlr_id($attraction_id);
        $new_title = sanitize_text_field($data['title']);
        $new_description = $this->prepare_for_wp_editor(isset($data['description']) ? $data['description'] : '');

        $post_args = array(
            'post_type' => 'trvlr_attraction',
            'post_status' => 'publish',
            'meta_input' => array(
                'trvlr_id' => $attraction_id,
                'trvlr_pk' => isset($data['pk']) ? $data['pk'] : '',
                'trvlr_product_type' => isset($data['product_type']) ? $data['product_type'] : '',
                'trvlr_raw_data' => json_encode($data),
                'trvlr_description' => $new_description,
                'trvlr_short_description' => isset($data['short_description']) ? $this->prepare_for_wp_editor($data['short_description']) : '',
                'trvlr_duration' => isset($data['duration']) ? sanitize_text_field($data['duration']) : '',
                'trvlr_additional_info' => isset($data['additional_info']) ? $this->prepare_for_wp_editor($data['additional_info']) : '',
                'trvlr_start_time' => '',
                'trvlr_end_time' => '',
                'trvlr_is_on_sale' => isset($data['is_on_sale']) ? (bool) $data['is_on_sale'] : false,
                'trvlr_sale_discount' => isset($data['sale_discount']) ? sanitize_text_field($data['sale_discount']) : '',
                'trvlr_sale_description' => isset($data['sale_description']) ? sanitize_text_field($data['sale_description']) : '',
            ),
        );

        // Pricing
        $pricing_rows = array();
        if (!empty($data['pricing']) && is_array($data['pricing'])) {
            foreach ($data['pricing'] as $p) {
                $name = isset($p['pricing_type']) ? $p['pricing_type'] : (isset($p['extra_data']['name']) ? $p['extra_data']['name'] : '');
                $amount = isset($p['extra_data']['price']['amount']) ? $p['extra_data']['price']['amount'] : (isset($p['max_price']) ? $p['max_price'] : '');
                $pricing_rows[] = array(
                    'type' => sanitize_text_field($name),
                    'price' => sanitize_text_field($amount),
                    'sale_price' => '',
                );
            }
        }
        $post_args['meta_input']['trvlr_pricing'] = $pricing_rows;

        // Locations
        $location_rows = array();
        if (!empty($data['location_start'])) {
            $loc_start = json_decode($data['location_start'], true);
            if (is_array($loc_start) && !empty($loc_start[0])) {
                $l = $loc_start[0];
                $location_rows[] = array(
                    'type' => 'Start',
                    'address' => isset($l['building']) ? $l['building'] . ', ' . $l['city'] : '',
                    'lat' => isset($l['latitude']) ? $l['latitude'] : '',
                    'lng' => isset($l['longitude']) ? $l['longitude'] : '',
                );
            }
        } elseif (!empty($data['location']['coordinates'])) {
            $coords = $data['location']['coordinates'];
            $location_rows[] = array(
                'type' => 'Start',
                'address' => isset($data['location']['address']) ? $data['location']['address'] : '',
                'lat' => isset($coords[1]) ? $coords[1] : '',
                'lng' => isset($coords[0]) ? $coords[0] : '',
            );
        }
        $post_args['meta_input']['trvlr_locations'] = $location_rows;

        // Inclusions & Highlights
        $post_args['meta_input']['trvlr_inclusions'] = !empty($data['inclusions']) ? $this->prepare_for_wp_editor($data['inclusions']) : '';
        $post_args['meta_input']['trvlr_highlights'] = !empty($data['highlights']) ? $this->prepare_for_wp_editor($data['highlights']) : '';

        $skipped_fields = array();
        $status = 'updated';

        // Handle existing post - check for custom edits
        if ($existing_post) {
            $existing_edited_fields = get_post_meta($existing_post->ID, '_trvlr_edited_fields', true);
            if (!is_array($existing_edited_fields)) {
                $existing_edited_fields = array();
            }

            $force_sync_fields = $this->get_force_sync_fields($existing_post->ID);
            $force_sync_title = in_array('post_title', $force_sync_fields);

            // Smart diffing for title
            if (!$force_sync_title && in_array('post_title', $existing_edited_fields)) {
                $skipped_fields[] = 'post_title';
            } else {
                $current_title_hash = md5($existing_post->post_title);
                $last_synced_title_hash = get_post_meta($existing_post->ID, '_trvlr_sync_hash_post_title', true);

                if (!$force_sync_title && $last_synced_title_hash && $current_title_hash !== $last_synced_title_hash) {
                    $skipped_fields[] = 'post_title';
                    $this->mark_field_as_edited($existing_post->ID, 'post_title');
                } else {
                    $post_args['post_title'] = $new_title;
                }
            }

            // Smart diffing for other meta fields
            $trackable_fields = Trvlr_Field_Map::get_field_names();
            foreach ($trackable_fields as $field_name) {
                if ($field_name === 'post_title') continue;

                $force_sync_field = in_array($field_name, $force_sync_fields);

                if (!$force_sync_field && in_array($field_name, $existing_edited_fields)) {
                    $skipped_fields[] = $field_name;
                    if (isset($post_args['meta_input'][$field_name])) {
                        unset($post_args['meta_input'][$field_name]);
                    }
                    continue;
                }

                $current_value = Trvlr_Field_Map::get_field_value($existing_post->ID, $field_name);
                $synced_hash = get_post_meta($existing_post->ID, "_trvlr_sync_hash_{$field_name}", true);

                if ($synced_hash) {
                    $current_hash = Trvlr_Field_Map::hash_field_value($current_value, $field_name);

                    if ($current_hash !== $synced_hash) {
                        if (!$force_sync_field) {
                            $skipped_fields[] = $field_name;
                            $this->mark_field_as_edited($existing_post->ID, $field_name);
                            if (isset($post_args['meta_input'][$field_name])) {
                                unset($post_args['meta_input'][$field_name]);
                            }
                        }
                    }
                }
            }

            $post_args['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_args);

            if (!empty($skipped_fields)) {
                $status = 'skipped';
                Trvlr_Logger::log('attraction_skipped', "Skipped: {$new_title} (custom edits)", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'skipped_fields' => $skipped_fields
                ));
            } else {
                Trvlr_Logger::log('attraction_updated', "Updated: {$new_title}", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id
                ));
            }

            if (!empty($force_sync_fields)) {
                $this->clear_force_synced_fields($post_id, $force_sync_fields);
            }
        } else {
            $post_args['post_title'] = $new_title;
            $post_id = wp_insert_post($post_args);
            $status = 'created';

            Trvlr_Logger::log('attraction_created', "Created: {$new_title}", array(
                'post_id' => $post_id,
                'trvlr_id' => $attraction_id
            ));
        }

        if (!is_wp_error($post_id)) {
            if (!empty($data['images']['all_images']) && is_array($data['images']['all_images'])) {
                $force_sync_fields = $this->get_force_sync_fields($post_id);
                $this->process_images($post_id, $data['images']['all_images'], $skipped_fields, $force_sync_fields);
            }

            $this->store_field_hashes($post_id, $skipped_fields);

            return $status;
        } else {
            $error_msg = "Failed to sync attraction: {$new_title}";
            Trvlr_Logger::log('error', $error_msg, array(
                'trvlr_id' => $attraction_id,
                'error' => $post_id->get_error_message()
            ));

            Trvlr_Notifier::notify_sync_error(
                $error_msg . ': ' . $post_id->get_error_message(),
                array('attraction_id' => $attraction_id)
            );

            return 'error';
        }
    }

    private function mark_field_as_edited($post_id, $field_name)
    {
        $edited_fields = get_post_meta($post_id, '_trvlr_edited_fields', true);
        if (!is_array($edited_fields)) {
            $edited_fields = array();
        }

        if (!in_array($field_name, $edited_fields)) {
            $edited_fields[] = $field_name;
            update_post_meta($post_id, '_trvlr_edited_fields', $edited_fields);
            update_post_meta($post_id, '_trvlr_has_custom_edits', '1');
        }
    }

    private function get_force_sync_fields($post_id)
    {
        $force_sync = get_post_meta($post_id, '_trvlr_force_sync_fields', true);
        return is_array($force_sync) ? $force_sync : array();
    }

    private function store_field_hashes($post_id, $skipped_fields = array())
    {
        if (!class_exists('Trvlr_Field_Map')) {
            return;
        }

        $trvlr_id = get_post_meta($post_id, 'trvlr_id', true);
        $is_debug_post = ($trvlr_id == '5220');

        if ($is_debug_post) {
            error_log("===== TRVLR HASH STORAGE START [{$post_id}] =====");
            error_log("Skipped fields: " . implode(', ', $skipped_fields));
        }

        try {
            $trackable_fields = Trvlr_Field_Map::get_field_names();

            foreach ($trackable_fields as $field_name) {
                if (in_array($field_name, $skipped_fields)) {
                    if ($is_debug_post) {
                        error_log("  [{$field_name}] SKIPPED (user has edits)");
                    }
                    continue;
                }

                $saved_value = Trvlr_Field_Map::get_field_value($post_id, $field_name);

                if ($is_debug_post && in_array($field_name, ['trvlr_description', 'trvlr_short_description', 'trvlr_additional_info', 'trvlr_inclusions', 'trvlr_highlights'])) {
                    error_log("  [{$field_name}] FULL RAW VALUE:");
                    error_log("    Length: " . strlen($saved_value));
                    error_log("    First 200 chars: " . substr($saved_value, 0, 200));
                    error_log("    Last 200 chars: " . substr($saved_value, -200));
                    error_log("    Serialized: " . substr(serialize($saved_value), 0, 300));
                }

                $hash = Trvlr_Field_Map::hash_field_value($saved_value, $field_name);

                if ($is_debug_post) {
                    $display_value = $saved_value;
                    if (is_array($display_value)) {
                        $display_value = 'ARRAY(' . count($display_value) . ' items): ' . substr(json_encode($display_value), 0, 100);
                    } elseif (is_string($display_value) && strlen($display_value) > 100) {
                        $display_value = substr($display_value, 0, 100) . '...';
                    } elseif (empty($display_value)) {
                        $display_value = '(EMPTY)';
                    }

                    error_log("  [{$field_name}] Hash: " . substr($hash, 0, 12) . "... | Value: " . $display_value);
                }

                update_post_meta($post_id, "_trvlr_sync_hash_{$field_name}", $hash);
            }

            if ($is_debug_post) {
                error_log("===== TRVLR HASH STORAGE END [{$post_id}] =====");
            }
        } catch (Exception $e) {
            error_log('TRVLR Error in store_field_hashes: ' . $e->getMessage());
        }
    }

    private function clear_force_synced_fields($post_id, $force_synced_fields)
    {
        $edited_fields = get_post_meta($post_id, '_trvlr_edited_fields', true);
        if (!is_array($edited_fields)) {
            $edited_fields = array();
        }

        $edited_fields = array_diff($edited_fields, $force_synced_fields);

        if (empty($edited_fields)) {
            delete_post_meta($post_id, '_trvlr_edited_fields');
            delete_post_meta($post_id, '_trvlr_has_custom_edits');
        } else {
            update_post_meta($post_id, '_trvlr_edited_fields', $edited_fields);
        }

        delete_post_meta($post_id, '_trvlr_force_sync_fields');
    }

    public function clear_all_custom_edit_flags()
    {
        $args = array(
            'post_type' => 'trvlr_attraction',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => '_trvlr_has_custom_edits',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $posts = get_posts($args);

        foreach ($posts as $post_id) {
            delete_post_meta($post_id, '_trvlr_force_sync_fields');
        }

        return count($posts);
    }

    private function process_images($post_id, $images, $skipped_fields = array(), $force_sync_fields = array())
    {
        if (empty($images)) return;

        $gallery_ids = array();
        $first_image_id = null;

        // Check if we should skip media processing
        $skip_media = in_array('trvlr_media', $skipped_fields) && !in_array('trvlr_media', $force_sync_fields);
        $skip_thumbnail = in_array('_thumbnail_id', $skipped_fields) && !in_array('_thumbnail_id', $force_sync_fields);

        foreach ($images as $index => $img) {
            $image_url = isset($img['url']) ? $img['url'] : (is_string($img) ? $img : null);
            if (!$image_url) continue;

            global $wpdb;
            $attachment_id = $wpdb->get_var(
                $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'trvlr_source_url' AND meta_value = %s", $image_url)
            );

            if (!$attachment_id) {
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_id = media_sideload_image($image_url, $post_id, "Trvlr Image for Post $post_id", 'id');

                if (!is_wp_error($attachment_id)) {
                    update_post_meta($attachment_id, 'trvlr_source_url', $image_url);
                } else {
                    continue;
                }
            }

            if ($attachment_id) {
                $gallery_ids[] = $attachment_id;
                if ($index === 0) $first_image_id = $attachment_id;
            }
        }

        if (!empty($gallery_ids)) {
            update_post_meta($post_id, 'trvlr_gallery_ids', $gallery_ids);

            // Only update trvlr_media if not skipped or force synced
            if (!$skip_media) {
                update_post_meta($post_id, 'trvlr_media', $gallery_ids);
            }

            // Only update featured image if not skipped or force synced
            if (!$skip_thumbnail && $first_image_id) {
                set_post_thumbnail($post_id, $first_image_id);
            }
        }
    }

    private function get_post_by_trvlr_id($trvlr_id)
    {
        $args = array(
            'post_type' => 'trvlr_attraction',
            'meta_key' => 'trvlr_id',
            'meta_value' => $trvlr_id,
            'posts_per_page' => 1,
            'fields' => 'ids'
        );
        $query = new WP_Query($args);
        return $query->have_posts() ? get_post($query->posts[0]) : false;
    }

    /**
     * Prepare content for wp_editor storage
     * Converts HTML to plain text matching what wp_editor stores in the database
     * wp_editor saves plain text with \n line breaks, NOT HTML with <p> tags
     * 
     * This transformation is applied BEFORE saving so the database contains
     * the same format that the editor would produce when manually saving
     *
     * @param string $content Raw HTML content from API
     * @return string Plain text with line breaks matching wp_editor storage format
     */
    private function prepare_for_wp_editor($content)
    {
        if (empty($content)) {
            return '';
        }

        // Sanitize HTML first
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
}
