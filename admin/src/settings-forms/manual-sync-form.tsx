import React, { useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useTrvlr } from '../context/TrvlrContext';

export const ManualSyncForm = () => {
   const { refreshSyncStats } = useTrvlr();
   const [syncing, setSyncing] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
   const [progress, setProgress] = useState<{ processed: number; total: number; percentage: number; message: string } | null>(null);
   const pollingInterval = useRef<number | null>(null);

   const stopPolling = () => {
      if (pollingInterval.current) {
         clearInterval(pollingInterval.current);
         pollingInterval.current = null;
      }
   };

   const pollProgress = async () => {
      try {
         const response: any = await apiFetch({ path: '/trvlr/v1/sync/progress' });

         if (response.in_progress && response.progress) {
            setProgress(response.progress);
         } else if (response.status === 'stale') {
            stopPolling();
            setSyncing(false);
            setProgress(null);
            setMessage({ type: 'error', text: __('Sync appears to have stalled. Please try again.', 'trvlr') });
         } else {
            stopPolling();
            setSyncing(false);
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

            await refreshSyncStats();
         }
      } catch (error) {
         console.error('Error polling sync progress:', error);
      }
   };

   const startPolling = () => {
      if (pollingInterval.current) return;
      pollingInterval.current = window.setInterval(pollProgress, 2000);
   };

   const handleManualSync = async () => {
      setSyncing(true);
      setMessage(null);
      setProgress(null);

      try {
         const response: any = await apiFetch({
            path: '/trvlr/v1/sync/manual',
            method: 'POST'
         });

         if (response.total) {
            setProgress({ processed: 0, total: response.total, percentage: 0, message: __('Starting sync...', 'trvlr') });
         }

         startPolling();
      } catch (error) {
         setSyncing(false);
         const errorMessage = error?.message || error?.data?.message || __('Sync failed. Please check logs.', 'trvlr');
         setMessage({ type: 'error', text: errorMessage });
         console.error('Sync error:', error);
      }
   };

   useEffect(() => {
      const checkExistingSync = async () => {
         try {
            const response: any = await apiFetch({ path: '/trvlr/v1/sync/progress' });
            if (response.in_progress && response.progress) {
               setSyncing(true);
               setProgress(response.progress);
               startPolling();
            }
         } catch (e) {}
      };
      checkExistingSync();

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

         {syncing && progress && (
            <div style={{
               background: '#f0f0f1',
               border: '1px solid #c3c4c7',
               borderRadius: '4px',
               padding: '16px',
               marginBottom: '16px'
            }}>
               <div style={{ marginBottom: '12px', fontWeight: 600 }}>
                  {progress.percentage}% Complete
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
                     width: `${progress.percentage}%`,
                     transition: 'width 0.3s ease'
                  }} />
               </div>
               <div style={{ fontSize: '13px', color: '#50575e' }}>
                  {progress.processed} of {progress.total} attractions synced
               </div>
            </div>
         )}

         <Button
            variant="primary"
            onClick={handleManualSync}
            isBusy={syncing}
            disabled={syncing}
         >
            {syncing ? __('Syncing...', 'trvlr') : __('Sync Now', 'trvlr')}
         </Button>
      </div>
   );
};

