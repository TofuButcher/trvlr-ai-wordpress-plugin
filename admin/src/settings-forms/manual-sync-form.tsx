import React, { useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useTrvlr } from '../context/TrvlrContext';

type Progress = { processed: number; total: number; percentage: number; message: string };

export const ManualSyncForm = () => {
   const { refreshSyncStats, cancelSync } = useTrvlr();
   const [syncing, setSyncing] = useState(false);
   const [cancelling, setCancelling] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
   const [progress, setProgress] = useState<Progress | null>(null);
   const pollingInterval = useRef<number | null>(null);

   const stopPolling = () => {
      if (pollingInterval.current) {
         clearInterval(pollingInterval.current);
         pollingInterval.current = null;
      }
   };

   const finishWithResults = (response: any) => {
      stopPolling();
      setSyncing(false);
      setCancelling(false);
      setProgress(null);

      if (response.results) {
         const r = response.results;
         const parts: string[] = [];
         if (r.created > 0) parts.push(`${r.created} created`);
         if (r.updated > 0) parts.push(`${r.updated} updated`);
         if (r.skipped > 0) parts.push(`${r.skipped} skipped`);
         if (r.errors > 0) parts.push(`${r.errors} errors`);

         setMessage({
            type: r.errors > 0 ? 'error' : 'success',
            text: parts.length > 0
               ? `Sync completed: ${parts.join(', ')}.`
               : __('Sync completed successfully!', 'trvlr'),
         });
      } else {
         setMessage({ type: 'success', text: __('Sync completed successfully!', 'trvlr') });
      }
   };

   const pollProgress = async () => {
      try {
         const response: any = await apiFetch({ path: '/trvlr/v1/sync/progress' });

         if (response.in_progress) {
            setSyncing(true);
            if (response.progress) setProgress(response.progress);
            return;
         }

         if (response.status === 'stale') {
            stopPolling();
            setSyncing(false);
            setCancelling(false);
            setProgress(null);
            setMessage({ type: 'error', text: __('Sync appears to have stalled. You can cancel it and start again.', 'trvlr') });
            return;
         }

         if (response.status === 'cancelled') {
            stopPolling();
            setSyncing(false);
            setCancelling(false);
            setProgress(null);
            setMessage({ type: 'error', text: __('Sync was cancelled.', 'trvlr') });
            await refreshSyncStats();
            return;
         }

         finishWithResults(response);
         await refreshSyncStats();
      } catch (error) {
         console.error('Error polling sync progress:', error);
      }
   };

   const startPolling = () => {
      if (pollingInterval.current) return;
      pollingInterval.current = window.setInterval(pollProgress, 2000);
   };

   // Attach the UI to whatever the server reports is currently happening.
   // Used both on mount and as a fallback when a start request is rejected.
   const attachToExistingSync = async (): Promise<boolean> => {
      try {
         const response: any = await apiFetch({ path: '/trvlr/v1/sync/progress' });
         if (response.in_progress) {
            setSyncing(true);
            setProgress(response.progress || null);
            startPolling();
            return true;
         }
         if (response.status === 'stale') {
            setSyncing(true);
            setProgress(response.progress || null);
            startPolling();
            setMessage({ type: 'error', text: __('A previous sync appears to have stalled. You can cancel it and start again.', 'trvlr') });
            return true;
         }
      } catch (e) {}
      return false;
   };

   const startSync = async (path: string, startingMessage: string) => {
      setSyncing(true);
      setMessage(null);
      setProgress(null);

      try {
         const response: any = await apiFetch({ path, method: 'POST' });

         if (response.total) {
            setProgress({ processed: 0, total: response.total, percentage: 0, message: startingMessage });
         }
         startPolling();
      } catch (error: any) {
         // A common rejection is "a sync is already in progress" (often started
         // elsewhere). Rather than just erroring, attach to the live run so the
         // user sees progress and gets a Cancel control.
         const attached = await attachToExistingSync();
         if (!attached) {
            setSyncing(false);
            const errorMessage = error?.message || error?.data?.message || __('Sync failed. Please check logs.', 'trvlr');
            setMessage({ type: 'error', text: errorMessage });
            console.error('Sync error:', error);
         }
      }
   };

   const handleManualSync = () => startSync('/trvlr/v1/sync/manual', __('Starting sync...', 'trvlr'));
   const handleManualSyncNoMedia = () => startSync('/trvlr/v1/sync/manual-no-media', __('Starting sync (no media)...', 'trvlr'));

   const handleCancel = async () => {
      setCancelling(true);
      try {
         await cancelSync();
         await pollProgress();
      } catch (error) {
         console.error('Cancel sync error:', error);
         setCancelling(false);
      }
   };

   useEffect(() => {
      attachToExistingSync();
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
                  {progress ? `${progress.percentage}% Complete` : __('Sync in progress…', 'trvlr')}
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
                     ? `${progress.processed} of ${progress.total} attractions synced`
                     : __('Connecting to the running sync…', 'trvlr')}
               </div>
            </div>
         )}

         <div style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
            <Button
               variant="primary"
               onClick={handleManualSync}
               isBusy={syncing}
               disabled={syncing}
            >
               {syncing ? __('Syncing...', 'trvlr') : __('Sync Now', 'trvlr')}
            </Button>

            <Button
               variant="secondary"
               onClick={handleManualSyncNoMedia}
               isBusy={syncing}
               disabled={syncing}
            >
               {__('Sync ( no media )', 'trvlr')}
            </Button>

            {syncing && (
               <Button
                  variant="tertiary"
                  isDestructive
                  onClick={handleCancel}
                  isBusy={cancelling}
                  disabled={cancelling}
               >
                  {cancelling ? __('Cancelling...', 'trvlr') : __('Cancel', 'trvlr')}
               </Button>
            )}
         </div>
      </div>
   );
};
