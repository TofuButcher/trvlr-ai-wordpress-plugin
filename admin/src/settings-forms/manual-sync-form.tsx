import React, { useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useTrvlr } from '../context/TrvlrContext';

type Progress = { processed: number; total: number; percentage: number; message: string };

type ProgressResponse = {
   in_progress?: boolean;
   is_active?: boolean;
   can_start?: boolean;
   status?: string | null;
   progress?: Progress | null;
   results?: {
      created?: number;
      updated?: number;
      skipped?: number;
      errors?: number;
   } | null;
   driver?: string;
   schema?: number;
   notice?: { type?: string; message?: string; reason?: string } | null;
};

export const ManualSyncForm = () => {
   const { refreshSyncStats, cancelSync } = useTrvlr();
   const [syncing, setSyncing] = useState(false);
   const [cancelling, setCancelling] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
   const [progress, setProgress] = useState<Progress | null>(null);
   const pollingInterval = useRef<number | null>(null);
   const actionLock = useRef(false);
   const expectSyncUntil = useRef(0);

   const stopPolling = () => {
      if (pollingInterval.current) {
         clearInterval(pollingInterval.current);
         pollingInterval.current = null;
      }
   };

   const clearLocalSyncUi = (notice?: { type: 'success' | 'error'; text: string }) => {
      stopPolling();
      expectSyncUntil.current = 0;
      setSyncing(false);
      setCancelling(false);
      setProgress(null);
      if (notice) setMessage(notice);
   };

   const applyNotice = (response: ProgressResponse) => {
      if (response.notice?.message) {
         setMessage({
            type: response.notice.type === 'error' ? 'error' : 'success',
            text: response.notice.message,
         });
      }
   };

   const finishWithResults = (response: ProgressResponse) => {
      stopPolling();
      expectSyncUntil.current = 0;
      setSyncing(false);
      setCancelling(false);
      setProgress(null);

      if (response.results) {
         const r = response.results;
         const parts: string[] = [];
         if ((r.created ?? 0) > 0) parts.push(`${r.created} created`);
         if ((r.updated ?? 0) > 0) parts.push(`${r.updated} updated`);
         if ((r.skipped ?? 0) > 0) parts.push(`${r.skipped} skipped`);
         if ((r.errors ?? 0) > 0) parts.push(`${r.errors} errors`);

         setMessage({
            type: (r.errors ?? 0) > 0 ? 'error' : 'success',
            text: parts.length > 0
               ? `Sync completed: ${parts.join(', ')}.`
               : __('Sync completed successfully!', 'trvlr'),
         });
      } else {
         setMessage({ type: 'success', text: __('Sync completed successfully!', 'trvlr') });
      }
   };

   const beginTracking = (nextProgress?: Progress | null) => {
      expectSyncUntil.current = Date.now() + 120000;
      setSyncing(true);
      if (nextProgress) setProgress(nextProgress);
      startPolling();
   };

   const pollProgress = async () => {
      if (actionLock.current) return;

      try {
         const response: ProgressResponse = await apiFetch({ path: '/trvlr/v1/sync/progress' });
         if (actionLock.current) return;

         if (response.in_progress) {
            expectSyncUntil.current = Date.now() + 120000;
            setSyncing(true);
            if (response.progress) setProgress(response.progress);
            return;
         }

         if (response.status === 'completed') {
            finishWithResults(response);
            await refreshSyncStats();
            return;
         }

         // After Sync Now, progress can briefly lag behind the claim write — keep UI.
         if (Date.now() < expectSyncUntil.current) {
            return;
         }

         clearLocalSyncUi(
            response.notice?.message
               ? {
                    type: response.notice.type === 'error' ? 'error' : 'success',
                    text: response.notice.message,
                 }
               : undefined
         );
      } catch (error) {
         console.error('Error polling sync progress:', error);
      }
   };

   const startPolling = () => {
      if (pollingInterval.current) return;
      pollingInterval.current = window.setInterval(pollProgress, 2000);
   };

   const attachToActiveSync = async (): Promise<boolean> => {
      try {
         const response: ProgressResponse = await apiFetch({ path: '/trvlr/v1/sync/progress' });
         applyNotice(response);
         if (response.in_progress) {
            beginTracking(response.progress || null);
            return true;
         }
      } catch (e) {}
      return false;
   };

   const startSync = async (path: string, startingMessage: string, allowRetry = true) => {
      actionLock.current = true;
      stopPolling();
      setSyncing(true);
      setCancelling(false);
      setMessage(null);
      setProgress({ processed: 0, total: 0, percentage: 0, message: startingMessage });
      expectSyncUntil.current = Date.now() + 120000;

      try {
         const response: any = await apiFetch({ path, method: 'POST' });

         if (response.total) {
            setProgress({
               processed: 0,
               total: response.total,
               percentage: 0,
               message: response.message || startingMessage,
            });
         }
         actionLock.current = false;
         beginTracking(
            response.total
               ? {
                    processed: 0,
                    total: response.total,
                    percentage: 0,
                    message: response.message || startingMessage,
                 }
               : { processed: 0, total: 0, percentage: 0, message: startingMessage }
         );
         await pollProgress();
      } catch (error: any) {
         actionLock.current = false;
         const errorMessage = error?.message || error?.data?.message || __('Sync failed. Please check logs.', 'trvlr');
         const already = error?.data?.already_in_progress || /already in progress/i.test(errorMessage);
         const embeddedProgress = error?.data?.progress as Progress | undefined;

         if (already && embeddedProgress) {
            beginTracking(embeddedProgress);
            setMessage({ type: 'success', text: __('Connected to the sync already in progress.', 'trvlr') });
            return;
         }

         const attached = await attachToActiveSync();
         if (attached) {
            setMessage({ type: 'success', text: __('Connected to the sync already in progress.', 'trvlr') });
            return;
         }

         if (already && allowRetry) {
            try {
               await apiFetch({ path: '/trvlr/v1/sync/reset', method: 'POST' });
               await startSync(path, startingMessage, false);
               return;
            } catch (e) {
               console.error('Reset-and-retry failed:', e);
            }
         }

         expectSyncUntil.current = 0;
         setSyncing(false);
         setProgress(null);
         setMessage({ type: 'error', text: errorMessage });
         console.error('Sync error:', error);
      }
   };

   const handleManualSync = () => startSync('/trvlr/v1/sync/manual', __('Starting sync...', 'trvlr'));
   const handleManualSyncNoMedia = () => startSync('/trvlr/v1/sync/manual-no-media', __('Starting sync (no media)...', 'trvlr'));

   const handleCancel = async () => {
      actionLock.current = true;
      stopPolling();
      expectSyncUntil.current = 0;
      setCancelling(true);
      try {
         await apiFetch({ path: '/trvlr/v1/sync/reset', method: 'POST' });
         clearLocalSyncUi({ type: 'success', text: __('Sync cleared. You can start a new sync.', 'trvlr') });
         actionLock.current = false;
         await refreshSyncStats();
      } catch (error: any) {
         console.error('Cancel sync error:', error);
         try {
            await cancelSync();
            clearLocalSyncUi({ type: 'success', text: __('Sync cleared. You can start a new sync.', 'trvlr') });
         } catch (e) {
            setMessage({
               type: 'error',
               text: error?.message || __('Could not clear sync. Try again.', 'trvlr'),
            });
            setCancelling(false);
         }
         actionLock.current = false;
      }
   };

   useEffect(() => {
      (async () => {
         const response: ProgressResponse = await apiFetch({ path: '/trvlr/v1/sync/progress' }).catch(() => null);
         if (!response) return;
         applyNotice(response);
         if (response.in_progress) {
            beginTracking(response.progress || null);
            return;
         }
         setSyncing(false);
         setProgress(null);
      })();
      return () => stopPolling();
   }, []);

   return (
      <div className="trvlr-settings-form">
         {message && (
            <Notice
               status={message.type}
               onRemove={() => setMessage(null)}
               isDismissible
            >
               {message.text}
            </Notice>
         )}

         {syncing && (
            <div style={{
               background: '#f0f0f1',
               border: '1px solid #c3c4c7',
               borderRadius: '4px',
               padding: '16px',
               marginBottom: '16px'
            }}>
               <div style={{ marginBottom: '12px', fontWeight: 600 }}>
                  {progress
                     ? (progress.total > 0 ? `${progress.percentage}% Complete` : (progress.message || __('Starting…', 'trvlr')))
                     : __('Sync in progress…', 'trvlr')}
               </div>
               <div style={{
                  background: '#fff',
                  height: '24px',
                  borderRadius: '4px',
                  overflow: 'hidden',
                  position: 'relative',
                  marginBottom: '8px'
               }}>
                  <div style={{
                     background: '#2271b1',
                     height: '100%',
                     width: `${progress?.percentage ?? 0}%`,
                     transition: 'width 0.3s ease'
                  }} />
               </div>
               <div style={{ fontSize: '13px', color: '#50575e' }}>
                  {progress
                     ? (progress.total > 0
                        ? `${progress.processed} of ${progress.total} attractions synced`
                        : (progress.message || __('Fetching attractions list…', 'trvlr')))
                     : __('Connecting to the running sync…', 'trvlr')}
               </div>
            </div>
         )}

         <div style={{ display: 'flex', gap: '8px', alignItems: 'center', flexWrap: 'wrap' }}>
            <Button
               variant="primary"
               onClick={handleManualSync}
               isBusy={syncing}
               disabled={syncing || cancelling}
            >
               {syncing ? __('Syncing...', 'trvlr') : __('Sync Now', 'trvlr')}
            </Button>

            <Button
               variant="secondary"
               onClick={handleManualSyncNoMedia}
               isBusy={syncing}
               disabled={syncing || cancelling}
            >
               {__('Sync ( no media )', 'trvlr')}
            </Button>

            <Button
               variant="tertiary"
               isDestructive
               onClick={handleCancel}
               isBusy={cancelling}
               disabled={cancelling}
            >
               {cancelling ? __('Clearing...', 'trvlr') : (syncing ? __('Cancel / Clear', 'trvlr') : __('Clear sync state', 'trvlr'))}
            </Button>
         </div>
      </div>
   );
};
