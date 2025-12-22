import { useState, useEffect, useRef } from '@wordpress/element';
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

   const pollProgress = async () => {
      try {
         const response = await apiFetch({ path: '/trvlr/v1/sync/progress' });

         if (response.in_progress && response.progress) {
            setProgress(response.progress);
         } else {
            if (pollingInterval.current) {
               clearInterval(pollingInterval.current);
               pollingInterval.current = null;
            }
            setSyncing(false);
            setProgress(null);
            setMessage({ type: 'success', text: __('Sync completed successfully!', 'trvlr') });
            await refreshSyncStats();
         }
      } catch (error) {
         console.error('Error polling sync progress:', error);
      }
   };

   const handleManualSync = async () => {
      setSyncing(true);
      setMessage(null);
      setProgress(null);

      pollingInterval.current = window.setInterval(pollProgress, 1000);

      try {
         await apiFetch({
            path: '/trvlr/v1/sync/manual',
            method: 'POST'
         });
      } catch (error) {
         if (pollingInterval.current) {
            clearInterval(pollingInterval.current);
            pollingInterval.current = null;
         }
         setSyncing(false);
         setProgress(null);

         const errorMessage = error?.message || error?.data?.message || 'Sync failed. Please check logs.';
         setMessage({ type: 'error', text: errorMessage });
         console.error('Sync error:', error);
      }
   };

   useEffect(() => {
      return () => {
         if (pollingInterval.current) {
            clearInterval(pollingInterval.current);
         }
      };
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

