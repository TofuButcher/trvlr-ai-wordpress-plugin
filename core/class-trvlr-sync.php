<?php

/**
 * Batched sync engine for TRVLR attractions (list cache + detail fetch).
 *
 * @package    Trvlr
 * @subpackage Trvlr/core
 */

if (!class_exists('Trvlr_Async')) {
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-trvlr-async.php';
}

class Trvlr_Sync
{
    const SYNC_STATE_OPTION = 'trvlr_sync_state';
    const SYNC_QUEUE_OPTION = 'trvlr_sync_queue';
    const BATCH_LOCK_TRANSIENT = 'trvlr_sync_batch_lock';
    const ATTRACTIONS_LIST_TRANSIENT_PREFIX = 'trvlr_attractions_list_';
    const ATTRACTIONS_LIST_TTL = 900;
    const BATCH_CRON_HOOK = 'trvlr_process_sync_batch';
    const DEFAULT_BATCH_SIZE = 2;
    const STALE_TIMEOUT = 600;
    const BATCH_LOCK_TTL = 130;

    private $attractions_list_memory = null;

    /**
     * Sync one attraction by WordPress post ID.
     *
     * @param int  $post_id          Attraction post ID.
     * @param bool $use_cached_list  Skip refreshing the all-attractions list.
     * @return array{success: bool, message: string, result?: string}
     */
    public function sync_single($post_id, $use_cached_list = false)
    {
        if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
            return array(
                'success' => false,
                'message' => 'Attraction syncing is disabled in TRVLR settings.',
            );
        }

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

        if (!$use_cached_list) {
            $list = $this->get_attractions_list(true);
            if (is_wp_error($list)) {
                return array(
                    'success' => false,
                    'message' => 'Failed to fetch attractions list: ' . $list->get_error_message(),
                );
            }
        }

        $list_item = $this->get_list_item_by_trvlr_id($trvlr_id, false);
        $this->apply_list_item_overrides($attraction_data, $list_item);

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

    /**
     * Sync attractions by TRVLR IDs (creates missing posts).
     *
     * @param int[] $trvlr_ids
     * @return array{success: bool, synced: int, errors: int, message: string}
     */
    public function sync_by_trvlr_ids(array $trvlr_ids): array
    {
        if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
            return array(
                'success' => false,
                'synced'   => 0,
                'errors'   => 0,
                'message'  => 'Attraction syncing is disabled in TRVLR settings.',
            );
        }

        $trvlr_ids = array_values(array_unique(array_filter(array_map('absint', $trvlr_ids))));
        if (empty($trvlr_ids)) {
            return array(
                'success' => false,
                'synced'  => 0,
                'errors'  => 0,
                'message' => 'No valid TRVLR IDs provided.',
            );
        }

        $list = $this->get_attractions_list(true);
        if (is_wp_error($list)) {
            return array(
                'success' => false,
                'synced'  => 0,
                'errors'  => count($trvlr_ids),
                'message' => 'Failed to fetch attractions list: ' . $list->get_error_message(),
            );
        }

        $synced = 0;
        $errors = 0;
        $list_by_id = $this->get_attractions_list_map(false);

        foreach ($trvlr_ids as $trvlr_id) {
            $existing_post = $this->get_post_by_trvlr_id($trvlr_id);

            if ($existing_post) {
                $result = $this->sync_single($existing_post->ID, true);
                if (empty($result['success'])) {
                    $errors++;
                } else {
                    $synced++;
                }
                continue;
            }

            $attraction_data = $this->fetch_single_attraction($trvlr_id);

            if (!$attraction_data) {
                $errors++;
                continue;
            }

            $list_item = isset($list_by_id[(int) $trvlr_id]) ? $list_by_id[(int) $trvlr_id] : null;
            $this->apply_list_item_overrides($attraction_data, $list_item);

            $result = $this->update_attraction_post($attraction_data);

            if ($result === 'error') {
                $errors++;
            } else {
                $synced++;
            }

            $cache_post = $this->get_post_by_trvlr_id($trvlr_id);
            if ($cache_post) {
                clean_post_cache($cache_post->ID);
            }
        }

        return array(
            'success' => $errors === 0,
            'synced'  => $synced,
            'errors'  => $errors,
            'message' => $errors === 0
                ? "Synced {$synced} attraction(s)."
                : "Synced {$synced} attraction(s), {$errors} error(s).",
        );
    }

    /**
     * Cron entry: start a full sync unless one is already in progress.
     *
     * @return void
     */
    public function sync_all()
    {
        $state = $this->get_sync_state();
        if ($state && $state['status'] === 'in_progress' && (time() - $state['last_batch_at']) < self::STALE_TIMEOUT) {
            return;
        }
        $this->start_sync();
    }

    /**
     * Start a full catalog sync (includes media).
     *
     * @return array{success: bool, message: string, total?: int, skip_media?: bool}
     */
    public function start_sync(): array
    {
        return $this->_start_sync_internal(false);
    }

    /**
     * Start a full catalog sync without downloading media.
     *
     * @return array{success: bool, message: string, total?: int, skip_media?: bool}
     */
    public function start_sync_no_media(): array
    {
        return $this->_start_sync_internal(true);
    }

    /**
     * Cancel an in-progress sync and clear queue/locks.
     *
     * @return array{success: bool, message: string}
     */
    public function cancel_sync(): array
    {
        $state = $this->get_sync_state();

        if (!$state || $state['status'] !== 'in_progress') {
            return array(
                'success' => false,
                'message' => 'No sync is currently in progress.',
            );
        }

        $state['status'] = 'cancelled';
        $state['message'] = 'Sync cancelled.';
        $this->save_sync_state($state);
        $this->delete_queue();
        $this->unschedule_batches();
        delete_transient(self::BATCH_LOCK_TRANSIENT);

        Trvlr_Logger::log('sync_cancelled', 'Sync cancelled by user', array(
            'user_id'       => get_current_user_id(),
            'processed'     => $state['current_index'],
            'total'         => $state['total'],
        ), $state['session_id']);

        return array(
            'success' => true,
            'message' => 'Sync cancelled.',
        );
    }

    /**
     * Shared start path for full sync and no-media sync.
     *
     * @param bool $skip_media When true, detail payloads drop images/list_image.
     * @return array{success: bool, message: string, total?: int, skip_media?: bool}
     */
    private function _start_sync_internal(bool $skip_media): array
    {
        if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
            return array(
                'success' => false,
                'message' => 'Attraction syncing is disabled in TRVLR settings.',
            );
        }

        $state = $this->get_sync_state();
        if ($state && $state['status'] === 'in_progress' && (time() - $state['last_batch_at']) < self::STALE_TIMEOUT) {
            return array(
                'success' => false,
                'message' => 'A sync is already in progress.',
            );
        }

        $session_id = 'sync_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 8);

        $api_data = $this->get_attractions_list(true);

        if (is_wp_error($api_data)) {
            Trvlr_Logger::log('error', 'API fetch failed: ' . $api_data->get_error_message());
            return array(
                'success' => false,
                'message' => 'Failed to fetch attractions: ' . $api_data->get_error_message(),
            );
        }

        if (empty($api_data['results'])) {
            Trvlr_Logger::log('error', 'No attractions found in API response');
            return array(
                'success' => false,
                'message' => 'No attractions found in API response',
            );
        }

        $total = count($api_data['results']);

        $sync_state = array(
            'session_id'    => $session_id,
            'current_index' => 0,
            'total'         => $total,
            'created'       => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'errors'        => 0,
            'status'        => 'in_progress',
            'skip_media'    => $skip_media,
            'started_at'    => time(),
            'last_batch_at' => time(),
            'percentage'    => 0,
            'message'       => 'Starting sync...',
        );

        $this->save_queue($api_data['results']);
        delete_transient(self::BATCH_LOCK_TRANSIENT);
        $this->save_sync_state($sync_state);

        $label = $skip_media ? 'Sync (no media) initiated' : 'Sync initiated';
        Trvlr_Logger::log('sync_start', $label, array(
            'user_id'    => get_current_user_id(),
            'total'      => $total,
            'skip_media' => $skip_media,
        ), $session_id);

        $this->schedule_next_batch();

        $mode_note = $skip_media ? ' (media skipped)' : '';
        return array(
            'success'    => true,
            'total'      => $total,
            'skip_media' => $skip_media,
            'message'    => "Sync started{$mode_note}. Processing {$total} attractions in batches.",
        );
    }

    /**
     * Process up to $batch_size queue items, then reschedule or complete.
     *
     * Yields early on memory/time budget; guarded by BATCH_LOCK_TRANSIENT.
     *
     * @param int|null $batch_size Null uses get_adaptive_batch_size().
     * @return void
     */
    public function process_batch(?int $batch_size = null): void
    {
        if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
            return;
        }

        if ($batch_size === null) {
            $batch_size = $this->get_adaptive_batch_size();
        }

        if (!$this->acquire_batch_lock()) {
            return;
        }

        try {
            $state = $this->get_sync_state();

            if (!$state || $state['status'] !== 'in_progress') {
                return;
            }

            $queue = $this->get_queue();
            if (empty($queue)) {
                $state['status'] = 'completed';
                $state['message'] = 'Sync ended early: work queue was unavailable.';
                $this->complete_sync($state);
                return;
            }

            $skip_media = !empty($state['skip_media']);

            $GLOBALS['trvlr_current_sync_session'] = $state['session_id'];

            @set_time_limit(120);

            $processed_in_batch = 0;
            $memory_limit = $this->get_memory_limit_bytes();
            $batch_start = microtime(true);
            $time_budget = $this->get_batch_time_budget();

            while ($state['current_index'] < $state['total'] && $processed_in_batch < $batch_size) {
                $fresh_state = $this->get_sync_state();
                if (!$fresh_state || $fresh_state['status'] !== 'in_progress') {
                    unset($GLOBALS['trvlr_current_sync_session']);
                    return;
                }

                if ($processed_in_batch > 0 && $memory_limit > 0 && memory_get_usage(true) > $memory_limit * 0.8) {
                    break;
                }
                if ($processed_in_batch > 0 && (microtime(true) - $batch_start) > $time_budget) {
                    break;
                }

                $index = $state['current_index'];
                $list_item = isset($queue[$index]) ? $queue[$index] : null;

                if (!is_array($list_item)) {
                    $state['errors']++;
                    $state['current_index']++;
                    $processed_in_batch++;
                    $this->save_progress_state($state);
                    continue;
                }

                $attraction_id = isset($list_item['pk']) ? $list_item['pk'] : (isset($list_item['id']) ? $list_item['id'] : 0);

                if (!$attraction_id) {
                    $state['errors']++;
                    $state['current_index']++;
                    $processed_in_batch++;
                    $this->save_progress_state($state);
                    continue;
                }

                $attraction_data = $this->fetch_single_attraction($attraction_id);

                if (!$attraction_data) {
                    Trvlr_Logger::log('error', "Failed to fetch details for attraction ID: {$attraction_id}");
                    $state['errors']++;
                    $state['current_index']++;
                    $processed_in_batch++;
                    $this->save_progress_state($state);
                    continue;
                }

                if (!$skip_media) {
                    if (!empty($list_item['images']) && empty($attraction_data['images']['all_images'])) {
                        $attraction_data['list_image'] = $list_item['images'];
                    }
                } else {
                    unset($attraction_data['images'], $attraction_data['list_image']);
                }

                $this->apply_list_item_overrides($attraction_data, $list_item);

                $result = $this->update_attraction_post($attraction_data);

                if ($result === 'created') $state['created']++;
                elseif ($result === 'updated' || $result === 'partial') $state['updated']++;
                elseif ($result === 'skipped' || $result === 'no_changes') $state['skipped']++;
                elseif ($result === 'error') $state['errors']++;

                $state['current_index']++;
                $processed_in_batch++;
                $this->save_progress_state($state);

                unset($attraction_data);
                $cache_post = $this->get_post_by_trvlr_id($attraction_id);
                if ($cache_post) {
                    clean_post_cache($cache_post->ID);
                }
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                $this->refresh_batch_lock();
            }

            if ($state['current_index'] >= $state['total']) {
                $this->complete_sync($state);
            } else {
                $this->save_progress_state($state);
                $this->schedule_next_batch();
            }

            unset($GLOBALS['trvlr_current_sync_session']);
        } catch (\Throwable $e) {
            error_log('TRVLR process_batch error: ' . $e->getMessage());
            if (class_exists('Trvlr_Logger')) {
                Trvlr_Logger::log('error', 'Batch processing error: ' . $e->getMessage());
            }
            $state = $this->get_sync_state();
            if ($state && $state['status'] === 'in_progress') {
                $this->save_progress_state($state);
                $this->schedule_next_batch();
            }
            unset($GLOBALS['trvlr_current_sync_session']);
        } finally {
            $this->release_batch_lock();
        }
    }

    /**
     * Finalize sync: log, notify, clear queue/batches, persist completed state.
     *
     * @param array $state
     * @return void
     */
    private function complete_sync(array $state): void
    {
        $state['status'] = 'completed';
        $state['percentage'] = 100;

        $message = sprintf(
            'Sync completed: %d created, %d updated, %d skipped%s',
            $state['created'],
            $state['updated'],
            $state['skipped'],
            $state['errors'] > 0 ? ", {$state['errors']} errors" : ''
        );

        Trvlr_Logger::log('sync_complete', $message, array(
            'created' => $state['created'],
            'updated' => $state['updated'],
            'skipped' => $state['skipped'],
            'errors'  => $state['errors'],
        ), $state['session_id']);

        Trvlr_Notifier::notify_sync_complete(
            $state['created'],
            $state['updated'],
            $state['skipped'],
            $state['errors']
        );

        $this->delete_queue();
        $this->unschedule_batches();
        $this->save_sync_state($state);
    }

    private function schedule_next_batch(): void
    {
        Trvlr_Async::queue_batch();
    }

    private function unschedule_batches(): void
    {
        Trvlr_Async::clear_batches();
    }

    /**
     * Reschedule a stuck in-progress sync when no batch is queued or locked.
     *
     * @return void
     */
    public function maybe_resume_sync(): void
    {
        $state = $this->get_sync_state();
        if (!$state || $state['status'] !== 'in_progress') {
            return;
        }

        $last_batch = isset($state['last_batch_at']) ? $state['last_batch_at'] : ($state['started_at'] ?? 0);
        if ((time() - $last_batch) > self::STALE_TIMEOUT) {
            return;
        }

        if (get_transient(self::BATCH_LOCK_TRANSIENT)) {
            return;
        }

        if (!Trvlr_Async::has_batch()) {
            Trvlr_Async::queue_batch_now();
        }
    }

    /**
     * Progress payload for the REST sync status endpoint.
     *
     * @return array{in_progress: bool, progress: array|null, status: string|null, results: array|null}
     */
    public function get_progress_status(): array
    {
        $state = $this->get_sync_state();

        if (!is_array($state)) {
            return array(
                'in_progress' => false,
                'progress'    => null,
                'status'      => null,
                'results'     => null,
            );
        }

        $status = isset($state['status']) ? $state['status'] : null;
        $processed = isset($state['current_index']) ? (int) $state['current_index'] : 0;
        $total = isset($state['total']) ? (int) $state['total'] : 0;
        $percentage = isset($state['percentage'])
            ? (int) $state['percentage']
            : ($total > 0 ? (int) round(($processed / $total) * 100) : 0);

        $results = null;
        if ($status === 'completed') {
            $results = array(
                'created' => isset($state['created']) ? (int) $state['created'] : 0,
                'updated' => isset($state['updated']) ? (int) $state['updated'] : 0,
                'skipped' => isset($state['skipped']) ? (int) $state['skipped'] : 0,
                'errors'  => isset($state['errors']) ? (int) $state['errors'] : 0,
            );
        } elseif ($status === 'in_progress') {
            $last_batch = isset($state['last_batch_at']) ? $state['last_batch_at'] : ($state['started_at'] ?? 0);
            if ((time() - $last_batch) > self::STALE_TIMEOUT) {
                $status = 'stale';
            } else {
                $this->maybe_resume_sync();
            }
        }

        return array(
            'in_progress' => $status === 'in_progress',
            'progress'    => array(
                'processed'  => $processed,
                'total'      => $total,
                'percentage' => $percentage,
                'message'    => isset($state['message']) ? $state['message'] : '',
            ),
            'status'      => $status,
            'results'     => $results,
        );
    }

    private function get_sync_state(): ?array
    {
        $state = get_option(self::SYNC_STATE_OPTION, null);
        return is_array($state) ? $state : null;
    }

    private function save_sync_state(array $state): void
    {
        update_option(self::SYNC_STATE_OPTION, $state, false);
    }

    /**
     * Persist sync counters, percentage, and message (no queue payload).
     *
     * @param array $state Sync state (modified in place).
     * @return void
     */
    private function save_progress_state(array &$state): void
    {
        $state['last_batch_at'] = time();
        $state['percentage'] = $state['total'] > 0
            ? (int) round(($state['current_index'] / $state['total']) * 100)
            : 0;
        $state['message'] = "Synced {$state['current_index']} of {$state['total']}";
        $this->save_sync_state($state);
    }

    private function get_queue(): array
    {
        $queue = get_option(self::SYNC_QUEUE_OPTION, array());
        return is_array($queue) ? $queue : array();
    }

    private function save_queue(array $queue): void
    {
        update_option(self::SYNC_QUEUE_OPTION, $queue, false);
    }

    private function delete_queue(): void
    {
        delete_option(self::SYNC_QUEUE_OPTION);
    }

    private function acquire_batch_lock(): bool
    {
        if (get_transient(self::BATCH_LOCK_TRANSIENT)) {
            return false;
        }
        set_transient(self::BATCH_LOCK_TRANSIENT, time(), self::BATCH_LOCK_TTL);
        return true;
    }

    private function refresh_batch_lock(): void
    {
        set_transient(self::BATCH_LOCK_TRANSIENT, time(), self::BATCH_LOCK_TTL);
    }

    private function release_batch_lock(): void
    {
        delete_transient(self::BATCH_LOCK_TRANSIENT);
    }

    /**
     * Per-batch item count from filter > option > memory/time heuristics.
     *
     * @return int
     */
    public function get_adaptive_batch_size(): int
    {
        $override = (int) get_option('trvlr_sync_batch_size', 0);

        if ($override > 0) {
            $size = $override;
        } else {
            $mem = $this->get_memory_limit_bytes();
            $mb = $mem > 0 ? $mem / 1048576 : 0;

            if ($mem === 0 || $mb >= 512) {
                $size = 20;
            } elseif ($mb >= 256) {
                $size = 10;
            } elseif ($mb >= 128) {
                $size = 5;
            } else {
                $size = self::DEFAULT_BATCH_SIZE;
            }

            $max_exec = (int) ini_get('max_execution_time');
            if ($max_exec > 0 && $max_exec <= 30) {
                $size = min($size, 5);
            }
        }

        $size = (int) apply_filters('trvlr_sync_batch_size', $size);

        return max(1, $size);
    }

    /**
     * Max seconds a batch may run before yielding (based on max_execution_time).
     *
     * @return float
     */
    private function get_batch_time_budget(): float
    {
        $max_exec = (int) ini_get('max_execution_time');
        $effective = $max_exec > 0 ? min($max_exec, 120) : 60;

        return (float) max(10, $effective * 0.6);
    }

    private function get_memory_limit_bytes(): int
    {
        $limit = ini_get('memory_limit');
        if ($limit === '-1') return 0;

        $unit = strtolower(substr(trim($limit), -1));
        $value = (int) $limit;
        $multipliers = array('k' => 1024, 'm' => 1048576, 'g' => 1073741824);

        if (isset($multipliers[$unit])) {
            $value *= $multipliers[$unit];
        }

        return $value;
    }

    private function get_attractions_list_cache_key()
    {
        $organisation_id = (string) get_option('trvlr_organisation_id', '');
        return self::ATTRACTIONS_LIST_TRANSIENT_PREFIX . md5($organisation_id);
    }

    /**
     * Cached all-attractions list (request memory + transient).
     *
     * @param bool $force_refresh
     * @return array{results: array}|WP_Error
     */
    private function get_attractions_list($force_refresh = false)
    {
        if (!$force_refresh && is_array($this->attractions_list_memory) && isset($this->attractions_list_memory['results'])) {
            return $this->attractions_list_memory;
        }

        $cache_key = $this->get_attractions_list_cache_key();

        if (!$force_refresh) {
            $cached = get_transient($cache_key);
            if (is_array($cached) && isset($cached['results']) && is_array($cached['results'])) {
                $this->attractions_list_memory = $cached;
                return $cached;
            }
        }

        $data = $this->fetch_attractions_from_api();

        if (is_wp_error($data)) {
            return $data;
        }

        $this->attractions_list_memory = $data;
        set_transient($cache_key, $data, self::ATTRACTIONS_LIST_TTL);

        return $data;
    }

    private function get_attractions_list_map($force_refresh = false)
    {
        $map = array();
        $list = $this->get_attractions_list($force_refresh);

        if (is_wp_error($list) || empty($list['results']) || !is_array($list['results'])) {
            return $map;
        }

        foreach ($list['results'] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = isset($item['pk']) ? (int) $item['pk'] : (isset($item['id']) ? (int) $item['id'] : 0);
            if ($id) {
                $map[$id] = $item;
            }
        }

        return $map;
    }

    private function get_list_item_by_trvlr_id($trvlr_id, $force_refresh = false)
    {
        $map = $this->get_attractions_list_map($force_refresh);
        $trvlr_id = (int) $trvlr_id;
        return isset($map[$trvlr_id]) ? $map[$trvlr_id] : null;
    }

    /**
     * Merge list-endpoint title and group_id onto a detail payload.
     *
     * @param array      $attraction_data
     * @param array|null $list_item
     * @return void
     */
    private function apply_list_item_overrides(array &$attraction_data, $list_item)
    {
        if (!is_array($list_item)) {
            return;
        }

        if (array_key_exists('group_id', $list_item)) {
            $attraction_data['group_id'] = $list_item['group_id'];
        }

        $list_title = '';
        if (!empty($list_item['title'])) {
            $list_title = $list_item['title'];
        } elseif (!empty($list_item['name'])) {
            $list_title = $list_item['name'];
        }

        if ($list_title !== '') {
            $attraction_data['title'] = $list_title;
        }
    }

    /**
     * Paginate the all-attractions list endpoint into a single results array.
     *
     * @return array{results: array}|WP_Error
     */
    private function fetch_attractions_from_api()
    {
        $api_url = 'https://sl.portal.traveloris.com/api/process/webapi_handler/generic_attractions';
        $headers = $this->get_api_headers();

        $page_size = 1000;
        $continue_threshold = (int) floor($page_size * 0.9);
        $max_pages = 50;

        $all = array();
        $seen = array();

        for ($page = 1; $page <= $max_pages; $page++) {
            $response = wp_remote_post($api_url, array(
                'headers' => $headers,
                'body'    => json_encode(array(
                    'page'      => $page,
                    'page_size' => $page_size,
                )),
                'timeout' => 60,
            ));

            if (is_wp_error($response)) {
                if ($page === 1) {
                    return $response;
                }
                Trvlr_Logger::log('error', "Attraction list page {$page} fetch failed: " . $response->get_error_message());
                break;
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (empty($data) || !isset($data['results'])) {
                if ($page === 1) {
                    return new WP_Error('invalid_response', 'Invalid API response format');
                }
                break;
            }

            $results = is_array($data['results']) ? $data['results'] : array();
            $count = count($results);

            if ($count === 0) {
                break;
            }

            foreach ($results as $item) {
                $key = null;
                if (isset($item['pk'])) {
                    $key = 'pk_' . $item['pk'];
                } elseif (isset($item['id'])) {
                    $key = 'id_' . $item['id'];
                }
                if ($key !== null) {
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                }
                $all[] = $item;
            }

            if ($count < $continue_threshold) {
                break;
            }
        }

        return array('results' => $all);
    }

    /**
     * Fetch one attraction detail payload by TRVLR ID.
     *
     * @param int|string $attraction_id
     * @return array|null
     */
    private function fetch_single_attraction($attraction_id)
    {
        $api_url = 'https://sl.portal.traveloris.com/api/process/webapi_handler/generic_attraction_with_id';
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

    /**
     * Create or update an attraction post from API detail data.
     *
     * Honours Custom Edit skips via _trvlr_edited_fields.
     *
     * @param array $data Attraction detail (+ list overrides).
     * @return string One of: created|updated|partial|skipped|no_changes|error
     */
    private function update_attraction_post($data)
    {
        $attraction_id = isset($data['pk']) ? $data['pk'] : (isset($data['id']) ? $data['id'] : 0);

        if (!$attraction_id) {
            Trvlr_Logger::log('error', 'Missing attraction ID');
            return 'error';
        }

        $existing_post = $this->get_post_by_trvlr_id($attraction_id);
        $new_title = Trvlr_Data_Transform::normalize_post_title_for_sync(isset($data['title']) ? $data['title'] : '');

        $has_images = !empty($data['images']['all_images']) || !empty($data['list_image']);
        $is_new_post = !$existing_post;

        $post_status = 'publish';
        if ($is_new_post && !$has_images) {
            $post_status = 'draft';
        } elseif ($existing_post) {
            $post_status = $existing_post->post_status;
        }

        $meta_input = array_merge(
            array(
                'trvlr_id' => $attraction_id,
                'trvlr_pk' => isset($data['pk']) ? $data['pk'] : '',
                'trvlr_raw_data' => json_encode($data),
                'trvlr_group_id' => isset($data['group_id']) && is_int($data['group_id']) ? $data['group_id'] : '',
            ),
            Trvlr_Field_Map::build_sync_meta_from_api($data)
        );

        $meta_input['trvlr_pricing'] = Trvlr_Data_Transform::build_pricing_rows_from_api(
            isset($data['pricing']) && is_array($data['pricing']) ? $data['pricing'] : array()
        );
        $meta_input['trvlr_locations'] = Trvlr_Data_Transform::build_location_rows_from_api($data);

        $post_args = array(
            'post_type' => 'trvlr_attraction',
            'post_status' => $post_status,
            'meta_input' => $meta_input,
        );

        $skipped_fields = array();
        $updated_fields = array();
        $status = 'updated';

        if (!defined('TRVLR_SYNCING')) {
            define('TRVLR_SYNCING', true);
        }

        if ($existing_post) {
            $custom_edits = trvlr_get_custom_edit_fields($existing_post->ID);

            if (in_array('post_title', $custom_edits, true)) {
                $skipped_fields[] = 'post_title';
            } else {
                $post_args['post_title'] = $new_title;
                if (Trvlr_Field_Map::hash_field_value($existing_post->post_title, 'post_title')
                    !== Trvlr_Field_Map::hash_field_value($new_title, 'post_title')) {
                    $updated_fields[] = 'post_title';
                }
            }

            foreach (Trvlr_Field_Map::get_syncable_field_names() as $field_name) {
                if ($field_name === 'post_title') {
                    continue;
                }

                if (in_array($field_name, array('trvlr_media', '_thumbnail_id'), true)) {
                    if (in_array($field_name, $custom_edits, true)) {
                        $skipped_fields[] = $field_name;
                    }
                    continue;
                }

                if (in_array($field_name, $custom_edits, true)) {
                    $skipped_fields[] = $field_name;
                    if (isset($post_args['meta_input'][$field_name])) {
                        unset($post_args['meta_input'][$field_name]);
                    }
                    continue;
                }

                if (!array_key_exists($field_name, $post_args['meta_input'])) {
                    continue;
                }

                $current_value = Trvlr_Field_Map::get_field_value($existing_post->ID, $field_name);
                $new_value = $post_args['meta_input'][$field_name];

                if (Trvlr_Field_Map::hash_field_value($current_value, $field_name)
                    !== Trvlr_Field_Map::hash_field_value($new_value, $field_name)) {
                    $updated_fields[] = $field_name;
                }
            }

            $post_args['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_args);

            if (!empty($skipped_fields) && !empty($updated_fields)) {
                $status = 'partial';
                Trvlr_Logger::log('attraction_updated', "Updated: {$new_title} (Skipped Custom Edits)", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'updated_fields' => $updated_fields,
                    'skipped_fields' => $skipped_fields
                ));
            } else if (!empty($skipped_fields) && empty($updated_fields)) {
                $status = 'skipped';
                Trvlr_Logger::log('no_updates', "No Updates: {$new_title} (Custom Edits)", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'skipped_fields' => $skipped_fields
                ));
            } else if (!empty($updated_fields)) {
                Trvlr_Logger::log('attraction_updated', "Updated: {$new_title}", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id,
                    'updated_fields' => $updated_fields
                ));
            } else {
                $status = 'no_changes';
                Trvlr_Logger::log('no_updates', "No Updates: {$new_title}", array(
                    'post_id' => $post_id,
                    'trvlr_id' => $attraction_id
                ));
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
                $list_image_url = is_string($data['list_image']) ? $data['list_image'] : $this->get_best_image_url($data['list_image']);
                update_post_meta($post_id, '_trvlr_list_image_cache', $list_image_url);
                $images_to_process[] = array('url' => $list_image_url);
            }

            if (!empty($data['images']['all_images']) && is_array($data['images']['all_images'])) {
                $images_to_process = array_merge($images_to_process, $data['images']['all_images']);
            }

            if (!empty($images_to_process)) {
                $image_updated_fields = $this->process_images($post_id, $images_to_process, $skipped_fields);
                if (!empty($image_updated_fields)) {
                    $updated_fields = array_merge($updated_fields, $image_updated_fields);
                }
            }

            if (!empty($data['attraction_type']) && is_array($data['attraction_type'])) {
                wp_set_object_terms($post_id, $data['attraction_type'], 'trvlr_attraction_tag');
            }

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

    /**
     * Clear Custom Edit flags sitewide.
     *
     * @return mixed
     */
    public function clear_all_custom_edit_flags()
    {
        return Trvlr_Custom_Edits::clear_all_sitewide();
    }

    /**
     * Sideload/dedupe images and update gallery + featured image when not skipped.
     *
     * @param int   $post_id
     * @param array $images
     * @param array $skipped_fields Field names in Custom Edit mode.
     * @return string[] Updated field names.
     */
    private function process_images($post_id, $images, $skipped_fields = array())
    {
        if (empty($images)) return array();

        $size_filter = function ($sizes) {
            unset($sizes['1536x1536'], $sizes['2048x2048']);
            return $sizes;
        };
        add_filter('intermediate_image_sizes_advanced', $size_filter);

        $gallery_ids = array();
        $first_image_id = null;
        $processed_urls = array();
        $images_changed = false;

        $skip_media = in_array('trvlr_media', $skipped_fields, true);
        $skip_thumbnail = in_array('_thumbnail_id', $skipped_fields, true);

        foreach ($images as $index => $img) {
            if (is_array($img)) {
                $image_url = $this->get_best_image_url($img);
            } else {
                $image_url = is_string($img) ? $img : null;
            }

            if (!$image_url) continue;

            $normalized_url = $this->normalize_image_url_for_dedup($image_url);
            if (in_array($normalized_url, $processed_urls)) continue;
            $processed_urls[] = $normalized_url;

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
            $existing_gallery = get_post_meta($post_id, 'trvlr_media', true);
            if (!is_array($existing_gallery)) {
                $existing_gallery = array();
            }

            sort($gallery_ids);
            sort($existing_gallery);
            $gallery_changed = ($gallery_ids !== $existing_gallery);

            update_post_meta($post_id, 'trvlr_gallery_ids', $gallery_ids);

            if (!$skip_media && ($gallery_changed || $images_changed)) {
                update_post_meta($post_id, 'trvlr_media', $gallery_ids);
                $updated_fields[] = 'trvlr_media';
            }

            $existing_thumbnail = get_post_thumbnail_id($post_id);
            $thumbnail_changed = ($existing_thumbnail != $first_image_id);

            if (!$skip_thumbnail && $first_image_id && ($thumbnail_changed || $images_changed)) {
                set_post_thumbnail($post_id, $first_image_id);
                $updated_fields[] = '_thumbnail_id';
            }
        }

        remove_filter('intermediate_image_sizes_advanced', $size_filter);

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

    private function normalize_image_url_for_dedup($url)
    {
        return preg_replace('/_lg(\.(jpg|jpeg|png|gif))$/i', '$1', $url);
    }

    private function download_image_with_original_filename($image_url, $post_id)
    {
        $parsed_url = parse_url($image_url);
        $original_filename = basename($parsed_url['path']);

        $tmp = download_url($image_url, 30);

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

}
