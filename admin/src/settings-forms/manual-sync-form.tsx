import React from '@wordpress/element';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const ManualSyncForm = () => {
   const { triggerManualSync } = useTrvlr();
   const [syncing, setSyncing] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   const handleManualSync = async () => {
      setSyncing(true);
      setMessage(null);

      const result = await triggerManualSync();

      if (result.success) {
         setMessage({ type: 'success', text: __('Sync completed successfully!', 'trvlr') });
      } else {
         setMessage({ type: 'error', text: __('Sync failed. Please check logs.', 'trvlr') });
      }

      setSyncing(false);
   };

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

