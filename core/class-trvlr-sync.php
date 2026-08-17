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
    const SYNC_NOTICE_TRANSIENT = 'trvlr_sync_notice';
    const ATTRACTIONS_LIST_TRANSIENT_PREFIX = 'trvlr_attractions_list_';
    const ATTRACTIONS_LIST_TTL = 900;
    const BATCH_CRON_HOOK = 'trvlr_process_sync_batch';
    const DEFAULT_BATCH_SIZE = 2;
    const STALE_TIMEOUT = 600;
    const ACTIVE_WINDOW = 120;
    const STARTING_WINDOW = 600;
    const STALL_TIMEOUT = 180;
    const BATCH_LOCK_TTL = 130;
    const SYNC_STATE_SCHEMA = 3;

    private $attractions_list_memory = null;
    private $time_budget_override = null;

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
                'message' => 'Attraction syncing is disabled in Traveloris settings.',
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
                'message'  => 'Attraction syncing is disabled in Traveloris settings.',
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
     * Cron entry: start a full sync unless one is already active.
     *
     * @return void
     */
    public function sync_all()
    {
        if ($this->is_sync_active()) {
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
     * Hard-reset any sync bookkeeping. Always succeeds.
     *
     * Prefer this over preserving zombie/stale/legacy state — recoverability
     * beats continuity when the sync engine changes.
     *
     * @return array{success: bool, message: string}
     */
    public function cancel_sync(): array
    {
        $state = $this->read_raw_sync_state();
        $session_id = is_array($state) ? ($state['session_id'] ?? null) : null;
        $processed = is_array($state) ? ($state['current_index'] ?? 0) : 0;
        $total = is_array($state) ? ($state['total'] ?? 0) : 0;
        $had_state = is_array($state);

        $this->nuke_sync_state('cancel');

        if ($had_state) {
            Trvlr_Logger::log('sync_cancelled', 'Sync state nuked by user cancel', array(
                'user_id'   => get_current_user_id(),
                'processed' => $processed,
                'total'     => $total,
                'driver'    => Trvlr_Async::driver(),
            ), $session_id);
        }

        return array(
            'success' => true,
            'message' => $had_state ? 'Sync cancelled.' : 'No sync is currently in progress.',
            'nuked'   => true,
        );
    }

    /**
     * Shared start path for full sync and no-media sync.
     *
     * Claims the sync slot before the slow list fetch so progress polls stop
     * reporting the previous stalled run mid-request.
     *
     * @param bool $skip_media When true, detail payloads drop images/list_image.
     * @return array{success: bool, message: string, total?: int, skip_media?: bool}
     */
    private function _start_sync_internal(bool $skip_media): array
    {
        if (function_exists('trvlr_is_attraction_sync_disabled') && trvlr_is_attraction_sync_disabled()) {
            return array(
                'success' => false,
                'message' => 'Attraction syncing is disabled in Traveloris settings.',
            );
        }

        $state = $this->get_sync_state();
        if ($this->is_sync_active($state)) {
            $processed = isset($state['current_index']) ? (int) $state['current_index'] : 0;
            $total = isset($state['total']) ? (int) $state['total'] : 0;
            return array(
                'success'             => false,
                'already_in_progress' => true,
                'message'             => 'A sync is already in progress.',
                'session_id'          => $state['session_id'] ?? null,
                'progress'            => array(
                    'processed'  => $processed,
                    'total'      => $total,
                    'percentage' => $total > 0 ? (int) round(($processed / $total) * 100) : 0,
                    'message'    => $state['message'] ?? 'Sync in progress…',
                ),
            );
        }

        if ($state) {
            $this->nuke_sync_state('takeover');
        } else {
            $this->clear_sync_runtime();
        }

        $session_id = 'sync_' . date('YmdHis') . '_' . substr(md5(uniqid('', true)), 0, 8);
        $now = time();

        $claim = array(
            'schema'        => self::SYNC_STATE_SCHEMA,
            'session_id'    => $session_id,
            'current_index' => 0,
            'total'         => 0,
            'created'       => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'errors'        => 0,
            'status'        => 'in_progress',
            'phase'         => 'starting',
            'skip_media'    => $skip_media,
            'started_at'    => $now,
            'last_batch_at' => $now,
            'percentage'    => 0,
            'message'       => 'Fetching attractions list...',
        );
        $this->save_sync_state($claim);

        $api_data = $this->get_attractions_list(true);

        // Keep the claim alive across slow list fetches on shared hosting.
        $claim = $this->read_raw_sync_state();
        if (is_array($claim) && ($claim['session_id'] ?? null) === $session_id) {
            $claim['last_batch_at'] = time();
            $claim['message'] = 'Fetching attractions list...';
            $this->save_sync_state($claim);
        }

        if (!$this->session_still_active($session_id)) {
            return array(
                'success' => false,
                'message' => 'Sync was cancelled while starting.',
            );
        }

        if (is_wp_error($api_data)) {
            $this->fail_claimed_sync($session_id, 'Failed to fetch attractions: ' . $api_data->get_error_message());
            Trvlr_Logger::log('error', 'API fetch failed: ' . $api_data->get_error_message(), array(
                'driver' => Trvlr_Async::driver(),
            ), $session_id);
            return array(
                'success' => false,
                'message' => 'Failed to fetch attractions: ' . $api_data->get_error_message(),
            );
        }

        if (empty($api_data['results'])) {
            $this->fail_claimed_sync($session_id, 'No attractions found in API response');
            Trvlr_Logger::log('error', 'No attractions found in API response', array(
                'driver' => Trvlr_Async::driver(),
            ), $session_id);
            return array(
                'success' => false,
                'message' => 'No attractions found in API response',
            );
        }

        if (!$this->session_still_active($session_id)) {
            return array(
                'success' => false,
                'message' => 'Sync was cancelled while starting.',
            );
        }

        $total = count($api_data['results']);

        $this->save_queue($api_data['results']);

        $sync_state = array(
            'schema'        => self::SYNC_STATE_SCHEMA,
            'session_id'    => $session_id,
            'current_index' => 0,
            'total'         => $total,
            'created'       => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'errors'        => 0,
            'status'        => 'in_progress',
            'phase'         => 'processing',
            'skip_media'    => $skip_media,
            'started_at'    => $now,
            'last_batch_at' => time(),
            'percentage'    => 0,
            'message'       => 'Starting sync...',
        );
        $this->save_sync_state($sync_state);

        $label = $skip_media ? 'Sync (no media) initiated' : 'Sync initiated';
        Trvlr_Logger::log('sync_start', $label, array(
            'user_id'    => get_current_user_id(),
            'total'      => $total,
            'skip_media' => $skip_media,
            'driver'     => Trvlr_Async::driver(),
            'schema'     => self::SYNC_STATE_SCHEMA,
        ), $session_id);

        Trvlr_Async::create_runner_token();
        $this->schedule_next_batch($session_id);
        Trvlr_Async::queue_batch_now($session_id);

        $mode_note = $skip_media ? ' (media skipped)' : '';
        return array(
            'success'    => true,
            'total'      => $total,
            'skip_media' => $skip_media,
            'session_id' => $session_id,
            'message'    => "Sync started{$mode_note}. Processing {$total} attractions in batches.",
        );
    }

    /**
     * Process up to $batch_size queue items, then reschedule or complete.
     *
     * Yields early on memory/time budget; guarded by BATCH_LOCK_TRANSIENT.
     *
     * @param int|null    $batch_size  Null uses get_adaptive_batch_size().
     * @param string|null $session_id  Optional session from queued action; mismatch no-ops.
     * @return void
     */
    public function process_batch(?int $batch_size = null, ?string $session_id = null): void
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

        $batch_session = null;
        $continue_session = null;

        try {
            $state = $this->get_sync_state();

            if (!$state || ($state['status'] ?? '') !== 'in_progress') {
                return;
            }

            if ($session_id !== null && $session_id !== '' && ($state['session_id'] ?? '') !== $session_id) {
                Trvlr_Logger::log('sync_cancel_race', 'Batch skipped: session mismatch on queue args', array(
                    'queued_session' => $session_id,
                    'current_session' => $state['session_id'] ?? null,
                    'driver' => Trvlr_Async::driver(),
                ), $session_id);
                return;
            }

            $batch_session = $state['session_id'] ?? null;
            $queue = $this->get_queue();
            if (empty($queue)) {
                $total = (int) ($state['total'] ?? 0);
                $index = (int) ($state['current_index'] ?? 0);

                // Claim/starting phase has no queue yet — never mark complete.
                if ($total === 0 || ($state['phase'] ?? '') === 'starting') {
                    return;
                }

                // Queue lost mid-run (cache/DB); do not fake a successful complete.
                if ($index < $total) {
                    Trvlr_Logger::log('error', 'Sync queue missing mid-run; abandoning', array(
                        'index'  => $index,
                        'total'  => $total,
                        'driver' => Trvlr_Async::driver(),
                    ), $batch_session);
                    $this->abandon_stalled_sync('missing_queue');
                    return;
                }

                $state['message'] = 'Sync ended early: work queue was unavailable.';
                $this->complete_sync($state);
                return;
            }

            $skip_media = !empty($state['skip_media']);

            $GLOBALS['trvlr_current_sync_session'] = $batch_session;

            Trvlr_Logger::log('sync_batch_start', 'Processing sync batch', array(
                'index'  => $state['current_index'] ?? 0,
                'total'  => $state['total'] ?? 0,
                'driver' => Trvlr_Async::driver(),
            ), $batch_session);

            @set_time_limit(120);

            $processed_in_batch = 0;
            $memory_limit = $this->get_memory_limit_bytes();
            $batch_start = microtime(true);
            $time_budget = $this->get_batch_time_budget();
            $aborted = false;

            while ($state['current_index'] < $state['total'] && $processed_in_batch < $batch_size) {
                if (!$this->session_still_active($batch_session)) {
                    Trvlr_Logger::log('sync_cancel_race', 'Batch aborted mid-loop: sync no longer active for session', array(
                        'index'  => $state['current_index'] ?? 0,
                        'driver' => Trvlr_Async::driver(),
                    ), $batch_session);
                    $aborted = true;
                    break;
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
                    if (!$this->save_progress_state($state)) {
                        $aborted = true;
                        break;
                    }
                    continue;
                }

                $attraction_id = isset($list_item['pk']) ? $list_item['pk'] : (isset($list_item['id']) ? $list_item['id'] : 0);

                if (!$attraction_id) {
                    $state['errors']++;
                    $state['current_index']++;
                    $processed_in_batch++;
                    if (!$this->save_progress_state($state)) {
                        $aborted = true;
                        break;
                    }
                    continue;
                }

                $attraction_data = $this->fetch_single_attraction($attraction_id);

                if (!$attraction_data) {
                    Trvlr_Logger::log('error', "Failed to fetch details for attraction ID: {$attraction_id}");
                    $state['errors']++;
                    $state['current_index']++;
                    $processed_in_batch++;
                    if (!$this->save_progress_state($state)) {
                        $aborted = true;
                        break;
                    }
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
                if (!$this->save_progress_state($state)) {
                    $aborted = true;
                    break;
                }

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

            if (!$aborted && $this->session_still_active($batch_session)) {
                if ($state['current_index'] >= $state['total']) {
                    $this->complete_sync($state);
                } else {
                    if ($this->save_progress_state($state)) {
                        $this->schedule_next_batch($batch_session);
                        $continue_session = $batch_session;
                    }
                }
            }

            Trvlr_Logger::log('sync_batch_end', 'Finished sync batch', array(
                'index'            => $state['current_index'] ?? 0,
                'processed_batch'  => $processed_in_batch,
                'aborted'          => $aborted,
                'driver'           => Trvlr_Async::driver(),
            ), $batch_session);

            unset($GLOBALS['trvlr_current_sync_session']);
        } catch (\Throwable $e) {
            error_log('TRVLR process_batch error: ' . $e->getMessage());
            if (class_exists('Trvlr_Logger')) {
                Trvlr_Logger::log('error', 'Batch processing error: ' . $e->getMessage(), array(
                    'driver' => Trvlr_Async::driver(),
                ), $batch_session);
            }
            if ($batch_session && $this->session_still_active($batch_session)) {
                $fresh = $this->get_sync_state();
                if ($fresh && $this->save_progress_state($fresh)) {
                    $this->schedule_next_batch($batch_session);
                    $continue_session = $batch_session;
                }
            }
            unset($GLOBALS['trvlr_current_sync_session']);
        } finally {
            $this->release_batch_lock();
            if ($continue_session !== null) {
                // Kick the next batch after the lock is free so the spawned
                // runner can acquire it — no reliance on WP-Cron traffic.
                Trvlr_Async::loopback_runner($continue_session);
            }
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
        $session_id = $state['session_id'] ?? null;
        $fresh = $this->get_sync_state();
        if (
            !$fresh
            || ($fresh['status'] ?? '') !== 'in_progress'
            || ($fresh['session_id'] ?? null) !== $session_id
        ) {
            return;
        }

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
            'driver'  => Trvlr_Async::driver(),
        ), $session_id);

        Trvlr_Notifier::notify_sync_complete(
            $state['created'],
            $state['updated'],
            $state['skipped'],
            $state['errors']
        );

        $this->delete_queue();
        $this->unschedule_batches();
        Trvlr_Async::delete_runner_token();
        $this->save_sync_state($state);
    }

    private function schedule_next_batch(?string $session_id = null): void
    {
        if ($session_id === null || $session_id === '') {
            $state = $this->get_sync_state();
            $session_id = is_array($state) ? ($state['session_id'] ?? null) : null;
        }
        Trvlr_Async::queue_batch($session_id);
    }

    private function unschedule_batches(): void
    {
        Trvlr_Async::clear_batches();
    }

    /**
     * Whether a batch lock transient is currently held.
     */
    public function is_batch_lock_held(): bool
    {
        return (bool) get_transient(self::BATCH_LOCK_TRANSIENT);
    }

    /**
     * Seconds since last_batch_at (or started_at).
     *
     * @param array|null $state
     */
    public function heartbeat_age(?array $state = null): int
    {
        $state = $state ?? $this->get_sync_state();
        if (!$state) {
            return PHP_INT_MAX;
        }
        $last = isset($state['last_batch_at']) ? (int) $state['last_batch_at'] : (int) ($state['started_at'] ?? 0);
        return max(0, time() - $last);
    }

    /**
     * Truly running: lock held, or heartbeat still within the active window.
     * Starting claims (total=0) get a longer window for slow list fetches.
     *
     * @param array|null $state
     */
    public function is_sync_active(?array $state = null): bool
    {
        $state = $state ?? $this->get_sync_state();
        if (!$state || ($state['status'] ?? '') !== 'in_progress') {
            return false;
        }
        if ($this->is_batch_lock_held()) {
            return true;
        }
        $window = ((int) ($state['total'] ?? 0) === 0 || ($state['phase'] ?? '') === 'starting')
            ? self::STARTING_WINDOW
            : self::ACTIVE_WINDOW;
        return $this->heartbeat_age($state) < $window;
    }

    /**
     * in_progress but not active (zombie / stalled).
     *
     * @param array|null $state
     */
    public function is_sync_stalled(?array $state = null): bool
    {
        $state = $state ?? $this->get_sync_state();
        if (!$state || ($state['status'] ?? '') !== 'in_progress') {
            return false;
        }
        return !$this->is_sync_active($state);
    }

    /**
     * Drop a non-active sync entirely (no sticky stale status).
     *
     * @param string $reason takeover|stale_poll|schema|cancel|manual
     */
    public function abandon_stalled_sync(string $reason = 'stale_poll'): void
    {
        $state = $this->read_raw_sync_state();
        if (!is_array($state)) {
            $this->clear_sync_runtime();
            return;
        }

        $session_id = $state['session_id'] ?? null;
        $event = strpos($reason, 'takeover') === 0 ? 'sync_takeover' : 'sync_stale_cleared';
        $processed = $state['current_index'] ?? 0;
        $total = $state['total'] ?? 0;
        $heartbeat = $this->heartbeat_age($state);

        $this->nuke_sync_state($reason);

        set_transient(self::SYNC_NOTICE_TRANSIENT, array(
            'type'    => 'cleared',
            'reason'  => $reason,
            'message' => 'A previous sync was cleared so a new one can start.',
        ), 120);

        Trvlr_Logger::log($event, 'Sync state nuked', array(
            'reason'    => $reason,
            'processed' => $processed,
            'total'     => $total,
            'heartbeat' => $heartbeat,
            'driver'    => Trvlr_Async::driver(),
            'schema'    => self::SYNC_STATE_SCHEMA,
        ), $session_id);
    }

    /**
     * Delete sync state option + queue + lock + pending batches + runner token.
     *
     * @param string $reason
     */
    public function nuke_sync_state(string $reason = 'manual'): void
    {
        $this->delete_sync_state_row();
        $this->clear_sync_runtime();
        Trvlr_Async::delete_runner_token();
    }

    /**
     * Clear queue, batch lock, and pending batch jobs.
     */
    private function clear_sync_runtime(): void
    {
        $this->delete_queue();
        $this->unschedule_batches();
        delete_transient(self::BATCH_LOCK_TRANSIENT);
        $this->bust_sync_option_cache();
    }

    /**
     * Roll back a claimed start session after list fetch failure.
     */
    private function fail_claimed_sync(string $session_id, string $message): void
    {
        $fresh = $this->read_raw_sync_state();
        if (!$fresh || ($fresh['session_id'] ?? null) !== $session_id) {
            return;
        }
        $this->nuke_sync_state('start_failed');
        set_transient(self::SYNC_NOTICE_TRANSIENT, array(
            'type'    => 'error',
            'message' => $message,
        ), 120);
    }

    /**
     * @param string|null $session_id
     */
    private function session_still_active(?string $session_id): bool
    {
        if ($session_id === null || $session_id === '') {
            return false;
        }
        $fresh = $this->read_raw_sync_state();
        return is_array($fresh)
            && ($fresh['status'] ?? '') === 'in_progress'
            && ($fresh['session_id'] ?? null) === $session_id
            && (int) ($fresh['schema'] ?? 0) === self::SYNC_STATE_SCHEMA;
    }

    /**
     * Keep an in-progress sync moving when the batch chain has gone quiet.
     *
     * Nudges the runner (loopback + spawn_cron) whenever the lock is free and
     * the heartbeat is aging — even if a batch event is pending, since pending
     * events never fire on hosts without cron traffic. As a last resort,
     * processes a small batch inline so progress continues while the admin
     * page is polling.
     *
     * @return void
     */
    public function maybe_resume_sync(): void
    {
        $state = $this->get_sync_state();
        if (!$state || ($state['status'] ?? '') !== 'in_progress') {
            return;
        }

        if ($this->is_batch_lock_held()) {
            return;
        }

        // Starting claim: the list fetch request is still running; leave it alone.
        if (((int) ($state['total'] ?? 0)) === 0 || ($state['phase'] ?? '') === 'starting') {
            return;
        }

        $age = $this->heartbeat_age($state);
        if ($age < 8) {
            return;
        }

        $session = $state['session_id'] ?? null;

        Trvlr_Async::queue_batch_now($session);

        if ($age >= 30) {
            // Cron and loopback both appear dead — advance inline in this request.
            $this->run_inline_batch($session);
        }
    }

    /**
     * Process a small, time-boxed batch inside the current request (progress
     * poll fallback when no background runner is available).
     *
     * @param string|null $session
     * @return void
     */
    public function run_inline_batch(?string $session = null): void
    {
        $this->time_budget_override = 8.0;
        try {
            $this->process_batch(3, $session);
        } finally {
            $this->time_budget_override = null;
        }
    }

    /**
     * Progress payload for the REST sync status endpoint.
     *
     * @return array
     */
    public function get_progress_status(): array
    {
        $state = $this->get_sync_state();
        $driver = Trvlr_Async::driver();
        $notice = get_transient(self::SYNC_NOTICE_TRANSIENT);
        if ($notice) {
            delete_transient(self::SYNC_NOTICE_TRANSIENT);
        }

        $base = array(
            'in_progress' => false,
            'is_active'   => false,
            'can_start'   => true,
            'progress'    => null,
            'status'      => null,
            'results'     => null,
            'driver'      => $driver,
            'schema'      => self::SYNC_STATE_SCHEMA,
            'notice'      => is_array($notice) ? $notice : null,
        );

        if (!is_array($state)) {
            return $base;
        }

        $status = isset($state['status']) ? $state['status'] : null;
        $processed = isset($state['current_index']) ? (int) $state['current_index'] : 0;
        $total = isset($state['total']) ? (int) $state['total'] : 0;
        $percentage = isset($state['percentage'])
            ? (int) $state['percentage']
            : ($total > 0 ? (int) round(($processed / $total) * 100) : 0);

        $results = null;
        $is_active = false;

        if ($status === 'completed') {
            $results = array(
                'created' => isset($state['created']) ? (int) $state['created'] : 0,
                'updated' => isset($state['updated']) ? (int) $state['updated'] : 0,
                'skipped' => isset($state['skipped']) ? (int) $state['skipped'] : 0,
                'errors'  => isset($state['errors']) ? (int) $state['errors'] : 0,
            );
        } elseif ($status === 'in_progress') {
            if ($this->is_sync_stalled($state) || $this->heartbeat_age($state) > self::STALE_TIMEOUT) {
                $this->abandon_stalled_sync('stale_poll');
                $notice = get_transient(self::SYNC_NOTICE_TRANSIENT);
                if ($notice) {
                    delete_transient(self::SYNC_NOTICE_TRANSIENT);
                }
                return array_merge($base, array(
                    'notice' => is_array($notice) ? $notice : array(
                        'type'    => 'cleared',
                        'message' => 'A previous sync was cleared so a new one can start.',
                    ),
                ));
            }
            $is_active = true;
            $this->maybe_resume_sync();
        } elseif (in_array($status, array('stale', 'cancelled'), true)) {
            $this->nuke_sync_state('legacy_' . $status);
            return array_merge($base, array(
                'notice' => array(
                    'type'    => 'cleared',
                    'message' => 'Cleared leftover sync state. You can start a new sync.',
                ),
            ));
        }

        $in_progress = $status === 'in_progress' && $is_active;
        $can_start = !$in_progress;

        return array(
            'in_progress' => $in_progress,
            'is_active'   => $is_active,
            'can_start'   => $can_start,
            'progress'    => array(
                'processed'  => $processed,
                'total'      => $total,
                'percentage' => $percentage,
                'message'    => isset($state['message']) ? $state['message'] : '',
            ),
            'status'      => $in_progress ? 'in_progress' : $status,
            'results'     => $results,
            'driver'      => $driver,
            'schema'      => self::SYNC_STATE_SCHEMA,
            'notice'      => is_array($notice) ? $notice : null,
        );
    }

    /**
     * Raw option read via SQL (avoids object-cache ghosts on shared hosts).
     */
    private function read_raw_sync_state(): ?array
    {
        global $wpdb;
        $this->bust_sync_option_cache();
        $row = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            self::SYNC_STATE_OPTION
        ));
        if ($row === null || $row === false) {
            return null;
        }
        $state = maybe_unserialize($row);
        return is_array($state) ? $state : null;
    }

    private function get_sync_state(): ?array
    {
        $state = $this->read_raw_sync_state();
        if (!$state) {
            return null;
        }

        $schema = isset($state['schema']) ? (int) $state['schema'] : 0;
        if ($schema !== self::SYNC_STATE_SCHEMA) {
            $this->nuke_sync_state('schema_mismatch');
            set_transient(self::SYNC_NOTICE_TRANSIENT, array(
                'type'    => 'cleared',
                'reason'  => 'schema_mismatch',
                'message' => 'Cleared sync state from an older plugin version. You can start a new sync.',
            ), 120);
            Trvlr_Logger::log('sync_stale_cleared', 'Nuked sync state due to schema mismatch', array(
                'found_schema'   => $schema,
                'current_schema' => self::SYNC_STATE_SCHEMA,
                'driver'         => Trvlr_Async::driver(),
            ), $state['session_id'] ?? null);
            return null;
        }

        return $state;
    }

    private function save_sync_state(array $state): void
    {
        global $wpdb;
        if (!isset($state['schema'])) {
            $state['schema'] = self::SYNC_STATE_SCHEMA;
        }
        $value = maybe_serialize($state);
        $this->bust_sync_option_cache();

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT option_id FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            self::SYNC_STATE_OPTION
        ));

        if ($exists) {
            $wpdb->update(
                $wpdb->options,
                array(
                    'option_value' => $value,
                    'autoload'     => 'no',
                ),
                array('option_name' => self::SYNC_STATE_OPTION),
                array('%s', '%s'),
                array('%s')
            );
        } else {
            $wpdb->insert(
                $wpdb->options,
                array(
                    'option_name'  => self::SYNC_STATE_OPTION,
                    'option_value' => $value,
                    'autoload'     => 'no',
                ),
                array('%s', '%s', '%s')
            );
        }

        $this->bust_sync_option_cache();
    }

    private function delete_sync_state_row(): void
    {
        global $wpdb;
        $this->bust_sync_option_cache();
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name = %s",
            self::SYNC_STATE_OPTION
        ));
        $this->bust_sync_option_cache();
    }

    private function bust_sync_option_cache(): void
    {
        wp_cache_delete(self::SYNC_STATE_OPTION, 'options');
        wp_cache_delete(self::SYNC_QUEUE_OPTION, 'options');
        wp_cache_delete('notoptions', 'options');
        wp_cache_delete('alloptions', 'options');
    }

    /**
     * Persist sync counters when the session is still the active in-progress run.
     *
     * @param array $state Sync state (modified in place).
     * @return bool False if cancel/takeover won the race.
     */
    private function save_progress_state(array &$state): bool
    {
        $session_id = $state['session_id'] ?? null;
        $fresh = $this->read_raw_sync_state();

        if (
            !$fresh
            || ($fresh['status'] ?? '') !== 'in_progress'
            || ($fresh['session_id'] ?? null) !== $session_id
        ) {
            Trvlr_Logger::log('sync_cancel_race', 'Batch aborted progress write: sync no longer in progress for session', array(
                'fresh_status'  => is_array($fresh) ? ($fresh['status'] ?? null) : null,
                'fresh_session' => is_array($fresh) ? ($fresh['session_id'] ?? null) : null,
                'driver'        => Trvlr_Async::driver(),
            ), $session_id);
            return false;
        }

        $state['schema'] = self::SYNC_STATE_SCHEMA;
        $state['phase'] = 'processing';
        $state['last_batch_at'] = time();
        $state['percentage'] = $state['total'] > 0
            ? (int) round(($state['current_index'] / $state['total']) * 100)
            : 0;
        $state['message'] = "Synced {$state['current_index']} of {$state['total']}";
        $this->save_sync_state($state);
        return true;
    }

    private function get_queue(): array
    {
        global $wpdb;
        $this->bust_sync_option_cache();
        $row = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            self::SYNC_QUEUE_OPTION
        ));
        if ($row === null || $row === false) {
            return array();
        }
        $queue = maybe_unserialize($row);
        return is_array($queue) ? $queue : array();
    }

    private function save_queue(array $queue): void
    {
        global $wpdb;
        $value = maybe_serialize($queue);
        $this->bust_sync_option_cache();

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT option_id FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            self::SYNC_QUEUE_OPTION
        ));

        if ($exists) {
            $wpdb->update(
                $wpdb->options,
                array(
                    'option_value' => $value,
                    'autoload'     => 'no',
                ),
                array('option_name' => self::SYNC_QUEUE_OPTION),
                array('%s', '%s'),
                array('%s')
            );
        } else {
            $wpdb->insert(
                $wpdb->options,
                array(
                    'option_name'  => self::SYNC_QUEUE_OPTION,
                    'option_value' => $value,
                    'autoload'     => 'no',
                ),
                array('%s', '%s', '%s')
            );
        }

        $this->bust_sync_option_cache();
    }

    private function delete_queue(): void
    {
        global $wpdb;
        $this->bust_sync_option_cache();
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name = %s",
            self::SYNC_QUEUE_OPTION
        ));
        $this->bust_sync_option_cache();
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
        if ($this->time_budget_override !== null) {
            return (float) $this->time_budget_override;
        }

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

        if (array_key_exists('seo_metadata', $list_item)) {
            $attraction_data['seo_metadata'] = $list_item['seo_metadata'];
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

        if (array_key_exists('seo_metadata', $data)) {
            $seo_metadata = $data['seo_metadata'];
            if ($seo_metadata === null || $seo_metadata === '') {
                $meta_input['trvlr_seo_metadata'] = '';
            } elseif (is_array($seo_metadata)) {
                $meta_input['trvlr_seo_metadata'] = wp_json_encode($seo_metadata);
            } elseif (is_string($seo_metadata)) {
                $meta_input['trvlr_seo_metadata'] = $seo_metadata;
            } else {
                $meta_input['trvlr_seo_metadata'] = '';
            }
        }

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
