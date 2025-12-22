<?php

/**
 * Sync Engine - Handles syncing attractions from TRVLR API
 *
 * @package    Trvlr
 * @subpackage Trvlr/core
 */

class Trvlr_Sync
{
    public function sync_single($post_id)
    {
        $trvlr_id = get_post_meta($post_id, 'trvlr_id', true);

        if (!$trvlr_id) {
            return array(
                'success' => false,
                'message' => 'No TRVLR ID found for this attraction'
            );
        }

        $attraction_data = $this->fetch_single_attraction($trvlr_id);

        if (!$attraction_data) {
            return array(
                'success' => false,
                'message' => 'Failed to fetch attraction data from API'
            );
        }

        $list_image = get_post_meta($post_id, '_trvlr_list_image_cache', true);
        if ($list_image && empty($attraction_data['images']['all_images'])) {
            $attraction_data['list_image'] = $list_image;
        }

        $result = $this->update_attraction_post($attraction_data);

        if ($result === 'error') {
            return array(
                'success' => false,
                'message' => 'Error updating attraction'
            );
        }

        $status_message = $result === 'skipped'
            ? 'Attraction synced (some fields skipped due to custom edits)'
            : 'Attraction synced successfully';

        return array(
            'success' => true,
            'message' => $status_message,
            'result' => $result
        );
    }

    public function sync_all()
    {
        $session_id = 'sync_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 8);
        $GLOBALS['trvlr_current_sync_session'] = $session_id;

        set_transient('trvlr_sync_in_progress', true, 3600);
        $this->update_sync_progress(0, 0, 'Starting sync...');

        Trvlr_Logger::log('sync_start', 'Sync initiated', array('user_id' => get_current_user_id()), $session_id);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $api_data = $this->fetch_attractions_from_api();

        if (is_wp_error($api_data)) {
            Trvlr_Logger::log('error', 'API fetch failed: ' . $api_data->get_error_message());
            delete_transient('trvlr_sync_in_progress');
            delete_transient('trvlr_sync_progress');
            return;
        }

        if (empty($api_data['results'])) {
            Trvlr_Logger::log('error', 'No attractions found in API response');
            delete_transient('trvlr_sync_in_progress');
            delete_transient('trvlr_sync_progress');
            return;
        }

        $total = count($api_data['results']);
        $processed = 0;

        foreach ($api_data['results'] as $list_item) {
            $attraction_id = isset($list_item['pk']) ? $list_item['pk'] : (isset($list_item['id']) ? $list_item['id'] : 0);

            if (!$attraction_id) {
                $errors++;
                $processed++;
                $this->update_sync_progress($processed, $total, "Processing {$processed} of {$total}...");
                continue;
            }

            $attraction_data = $this->fetch_single_attraction($attraction_id);

            if (!$attraction_data) {
                Trvlr_Logger::log('error', "Failed to fetch details for attraction ID: {$attraction_id}");
                $errors++;
                $processed++;
                $this->update_sync_progress($processed, $total, "Processing {$processed} of {$total}...");
                continue;
            }

            if (!empty($list_item['images']) && empty($attraction_data['images']['all_images'])) {
                $attraction_data['list_image'] = $list_item['images'];
            }

            $result = $this->update_attraction_post($attraction_data);

            if ($result === 'created') $created++;
            elseif ($result === 'updated' || $result === 'partial') $updated++;
            elseif ($result === 'skipped' || $result === 'no_changes') $skipped++;
            elseif ($result === 'error') $errors++;

            $processed++;
            $this->update_sync_progress($processed, $total, "Synced {$processed} of {$total}");
        }

        $message = "Sync completed: {$created} created, {$updated} updated, {$skipped} skipped" . ($errors > 0 ? ", {$errors} errors" : "");
        Trvlr_Logger::log('sync_complete', $message, array(
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ), $session_id);

        Trvlr_Notifier::notify_sync_complete($created, $updated, $skipped, $errors);

        delete_transient('trvlr_sync_in_progress');
        delete_transient('trvlr_sync_progress');
        unset($GLOBALS['trvlr_current_sync_session']);
    }

    private function update_sync_progress($processed, $total, $message)
    {
        $percentage = $total > 0 ? round(($processed / $total) * 100) : 0;

        set_transient('trvlr_sync_progress', array(
            'processed' => $processed,
            'total' => $total,
            'percentage' => $percentage,
            'message' => $message
        ), 3600);
    }

    private function fetch_attractions_from_api()
    {
        $api_url = 'https://sl.portal.trvlr.ai/api/process/webapi_handler/generic_attractions';
        $headers = $this->get_api_headers();

        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body'    => json_encode(array(
                'page'      => 1,
                'page_size' => 1000
            )),
            'timeout' => 60
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || !isset($data['results'])) {
            return new WP_Error('invalid_response', 'Invalid API response format');
        }

        return $data;
    }

    private function fetch_single_attraction($attraction_id)
    {
        $api_url = 'https://sl.portal.trvlr.ai/api/process/webapi_handler/generic_attraction_with_id';
        $headers = $this->get_api_headers();

        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body'    => json_encode(array(
                'id' => $attraction_id
            )),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['results'][0])) {
            return $data['results'][0];
        }

        return null;
    }

    private function get_api_headers()
    {
        $headers = array(
            'Content-Type' => 'application/json',
        );

        $organisation_id = get_option('trvlr_organisation_id', '');

        if (!empty($organisation_id)) {
            $headers['Origin'] = 'https://' . sanitize_text_field($organisation_id) . '.trvlr.ai';
        } else {
            $headers['Origin'] = home_url();
        }

        return $headers;
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

        $has_images = !empty($data['images']['all_images']) || !empty($data['list_image']);
        $is_new_post = !$existing_post;

        $post_status = 'publish';
        if ($is_new_post && !$has_images) {
            $post_status = 'draft';
        } elseif ($existing_post) {
            $post_status = $existing_post->post_status;
        }

        $post_args = array(
            'post_type' => 'trvlr_attraction',
            'post_status' => $post_status,
            'meta_input' => array(
                'trvlr_id' => $attraction_id,
                'trvlr_pk' => isset($data['pk']) ? $data['pk'] : '',
                'trvlr_raw_data' => json_encode($data),
                'trvlr_description' => $new_description,
                'trvlr_short_description' => isset($data['short_description']) ? $this->prepare_for_wp_editor($data['short_description']) : '',
                'trvlr_duration' => isset($data['duration']) ? sanitize_text_field($data['duration']) : '',
                'trvlr_additional_info' => isset($data['additional_info']) ? $this->prepare_for_wp_editor($data['additional_info']) : '',
                'trvlr_start_time' => isset($data['start_time']) ? sanitize_text_field($data['start_time']) : '',
                'trvlr_end_time' => isset($data['end_time']) ? sanitize_text_field($data['end_time']) : '',
            ),
        );

        $pricing_rows = array();
        if (!empty($data['pricing']) && is_array($data['pricing'])) {
            foreach ($data['pricing'] as $p) {
                $pricing_rows[] = array(
                    'type' => isset($p['pricing_type']) ? sanitize_text_field($p['pricing_type']) : '',
                    'price' => isset($p['max_price']) ? sanitize_text_field($p['max_price']) : '',
                    'sale_price' => '',
                );
            }
        }
        $post_args['meta_input']['trvlr_pricing'] = $pricing_rows;

        $location_rows = array();
        if (!empty($data['location_start'])) {
            $loc_start = is_string($data['location_start']) ? json_decode($data['location_start'], true) : $data['location_start'];
            if (is_array($loc_start) && !empty($loc_start[0])) {
                $l = $loc_start[0];
                $location_rows[] = array(
                    'type' => 'Start',
                    'address' => isset($l['building']) ? $l['building'] . ', ' . (isset($l['city']) ? $l['city'] : '') : '',
                    'lat' => isset($l['latitude']) ? $l['latitude'] : '',
                    'lng' => isset($l['longitude']) ? $l['longitude'] : '',
                );
            }
        }
        if (!empty($data['location']['coordinates'])) {
            $coords = $data['location']['coordinates'];
            $location_rows[] = array(
                'type' => 'Start',
                'address' => isset($data['location']['address']) ? $data['location']['address'] : '',
                'lat' => isset($coords[0]) ? $coords[0] : '',
                'lng' => isset($coords[1]) ? $coords[1] : '',
            );
        }
        $post_args['meta_input']['trvlr_locations'] = $location_rows;

        $post_args['meta_input']['trvlr_inclusions'] = !empty($data['inclusions']) ? $this->prepare_for_wp_editor($data['inclusions']) : '';
        $post_args['meta_input']['trvlr_highlights'] = !empty($data['highlights']) ? $this->prepare_for_wp_editor($data['highlights']) : '';

        $skipped_fields = array();
        $updated_fields = array();
        $status = 'updated';

        // Handle existing post - check for custom edits
        if ($existing_post) {
            $existing_edited_fields = get_post_meta($existing_post->ID, '_trvlr_edited_fields', true);
            if (!is_array($existing_edited_fields)) {
                $existing_edited_fields = array();
            }

            $force_sync_fields = $this->get_force_sync_fields($existing_post->ID);
            $force_sync_title = in_array('post_title', $force_sync_fields);

            // Smart diffing for title - decode HTML entities for comparison
            if (!$force_sync_title && in_array('post_title', $existing_edited_fields)) {
                $skipped_fields[] = 'post_title';
            } else {
                $existing_title_decoded = html_entity_decode($existing_post->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $new_title_decoded = html_entity_decode($new_title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $current_title_hash = md5($existing_title_decoded);
                $last_synced_title_hash = get_post_meta($existing_post->ID, '_trvlr_sync_hash_post_title', true);

                if (!$force_sync_title && $last_synced_title_hash && $current_title_hash !== $last_synced_title_hash) {
                    $skipped_fields[] = 'post_title';
                    $this->mark_field_as_edited($existing_post->ID, 'post_title');
                } else {
                    // Check if title is actually different (after decoding)
                    if ($existing_title_decoded !== $new_title_decoded) {
                        $post_args['post_title'] = $new_title;
                        $updated_fields[] = 'post_title';
                    }
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

                // Get the new value from post_args
                $new_value = isset($post_args['meta_input'][$field_name]) ? $post_args['meta_input'][$field_name] : null;

                if ($synced_hash) {
                    $current_hash = Trvlr_Field_Map::hash_field_value($current_value, $field_name);
                    $new_hash = Trvlr_Field_Map::hash_field_value($new_value, $field_name);

                    if ($current_hash !== $synced_hash) {
                        if (!$force_sync_field) {
                            $skipped_fields[] = $field_name;
                            $this->mark_field_as_edited($existing_post->ID, $field_name);
                            if (isset($post_args['meta_input'][$field_name])) {
                                unset($post_args['meta_input'][$field_name]);
                            }
                        } else {
                            $updated_fields[] = $field_name;
                        }
                    } else if ($new_hash !== $synced_hash) {
                        // Field hasn't been edited locally, but API has new data
                        $updated_fields[] = $field_name;
                    }
                } else if ($new_value !== null) {
                    // No synced hash exists, this is a new field
                    $updated_fields[] = $field_name;
                }
            }

            $post_args['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_args);

            // Determine logging based on what actually happened
            if (!empty($skipped_fields) && !empty($updated_fields)) {
                // Some fields updated, some skipped
                $status = 'partial';
                Trvlr_Logger::log('attraction_updated', "Updated: {$new_title} (Skipped Custom Edits)", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'updated_fields' => $updated_fields,
                    'skipped_fields' => $skipped_fields
                ));
            } else if (!empty($skipped_fields)) {
                // All fields skipped, no updates made
                $status = 'skipped';
                Trvlr_Logger::log('no_updates', "No Updates: {$new_title} (Custom Edits)", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'skipped_fields' => $skipped_fields
                ));
            } else if (!empty($updated_fields)) {
                // Fields were actually updated
                Trvlr_Logger::log('attraction_updated', "Updated: {$new_title}", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'updated_fields' => $updated_fields
                ));
            } else {
                // No changes detected
                $status = 'no_changes';
                Trvlr_Logger::log('no_updates', "No Updates: {$new_title}", array(
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
            $images_to_process = array();

            if (!empty($data['list_image'])) {
                $list_image_url = $this->get_best_image_url($data['list_image']);
                $images_to_process[] = array('url' => $list_image_url);
            }

            if (!empty($data['images']['all_images']) && is_array($data['images']['all_images'])) {
                $images_to_process = array_merge($images_to_process, $data['images']['all_images']);
            }

            if (!empty($images_to_process)) {
                $force_sync_fields = $this->get_force_sync_fields($post_id);
                $image_updated_fields = $this->process_images($post_id, $images_to_process, $skipped_fields, $force_sync_fields);
                if (!empty($image_updated_fields)) {
                    $updated_fields = array_merge($updated_fields, $image_updated_fields);
                }
            }

            if (!empty($data['attraction_type']) && is_array($data['attraction_type'])) {
                wp_set_object_terms($post_id, $data['attraction_type'], 'trvlr_attraction_tag');
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

                // Decode HTML entities for title to ensure consistent hashing
                if ($field_name === 'post_title' && is_string($saved_value)) {
                    $saved_value = html_entity_decode($saved_value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }

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
        if (empty($images)) return array();

        $gallery_ids = array();
        $first_image_id = null;
        $processed_urls = array();
        $images_changed = false;

        $skip_media = in_array('trvlr_media', $skipped_fields) && !in_array('trvlr_media', $force_sync_fields);
        $skip_thumbnail = in_array('_thumbnail_id', $skipped_fields) && !in_array('_thumbnail_id', $force_sync_fields);

        foreach ($images as $index => $img) {
            if (is_array($img)) {
                $image_url = $this->get_best_image_url($img);
            } else {
                $image_url = is_string($img) ? $img : null;
            }

            if (!$image_url) continue;

            if (in_array($image_url, $processed_urls)) continue;
            $processed_urls[] = $image_url;

            global $wpdb;
            $attachment_id = $wpdb->get_var(
                $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'trvlr_source_url' AND meta_value = %s", $image_url)
            );

            if (!$attachment_id) {
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_id = $this->download_image_with_original_filename($image_url, $post_id);

                if ($attachment_id && !is_wp_error($attachment_id)) {
                    update_post_meta($attachment_id, 'trvlr_source_url', $image_url);
                    $images_changed = true;
                } else {
                    continue;
                }
            }

            if ($attachment_id) {
                $gallery_ids[] = $attachment_id;
                if ($index === 0) $first_image_id = $attachment_id;
            }
        }

        $updated_fields = array();

        if (!empty($gallery_ids)) {
            // Compare existing gallery with new gallery
            $existing_gallery = get_post_meta($post_id, 'trvlr_media', true);
            if (!is_array($existing_gallery)) {
                $existing_gallery = array();
            }

            // Check if galleries are different (compare sorted arrays)
            sort($gallery_ids);
            sort($existing_gallery);
            $gallery_changed = ($gallery_ids !== $existing_gallery);

            update_post_meta($post_id, 'trvlr_gallery_ids', $gallery_ids);

            // Only update trvlr_media if not skipped or force synced
            if (!$skip_media) {
                if ($gallery_changed || $images_changed) {
                    update_post_meta($post_id, 'trvlr_media', $gallery_ids);
                    $updated_fields[] = 'trvlr_media';
                }
            }

            // Check if featured image changed
            $existing_thumbnail = get_post_thumbnail_id($post_id);
            $thumbnail_changed = ($existing_thumbnail != $first_image_id);

            // Only update featured image if not skipped or force synced
            if (!$skip_thumbnail && $first_image_id) {
                if ($thumbnail_changed || $images_changed) {
                    set_post_thumbnail($post_id, $first_image_id);
                    $updated_fields[] = '_thumbnail_id';
                }
            }
        }

        return $updated_fields;
    }

    private function get_best_image_url($img)
    {
        if (is_string($img)) {
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $img, $matches)) {
                $lg_url = preg_replace('/\.' . preg_quote($matches[1], '/') . '$/i', '_lg.' . $matches[1], $img);
                return $lg_url;
            }
            return $img;
        }

        if (is_array($img)) {
            if (!empty($img['largeSizeUrl'])) {
                return $img['largeSizeUrl'];
            }
            if (!empty($img['itemUrl'])) {
                return $img['itemUrl'];
            }
            if (!empty($img['url'])) {
                return $img['url'];
            }
        }

        return null;
    }

    private function download_image_with_original_filename($image_url, $post_id)
    {
        $parsed_url = parse_url($image_url);
        $original_filename = basename($parsed_url['path']);

        $tmp = download_url($image_url);

        if (is_wp_error($tmp)) {
            return $tmp;
        }

        $file_array = array(
            'name' => $original_filename,
            'tmp_name' => $tmp
        );

        $attachment_id = media_handle_sideload($file_array, $post_id);

        if (is_wp_error($attachment_id)) {
            @unlink($file_array['tmp_name']);
            return $attachment_id;
        }

        return $attachment_id;
    }

    private function get_post_by_trvlr_id($trvlr_id)
    {
        $args = array(
            'post_type' => 'trvlr_attraction',
            'meta_key' => 'trvlr_id',
            'meta_value' => $trvlr_id,
            'posts_per_page' => 1,
            'post_status' => 'any',
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
