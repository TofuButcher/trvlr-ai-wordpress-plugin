import React from '@wordpress/element';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const DangerZoneForm = () => {
   const { deleteData } = useTrvlr();
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   const handleDelete = async (includeMedia: boolean) => {
      const confirmText = includeMedia
         ? __('Delete ALL data including images? This cannot be undone!', 'trvlr')
         : __('Delete all attraction posts? (Images will be kept)', 'trvlr');

      if (!confirm(confirmText)) return;

      setMessage(null);
      const result = await deleteData(includeMedia);

      if (result.success) {
         setMessage({ type: 'success', text: __('Data deleted successfully.', 'trvlr') });
      } else {
         setMessage({ type: 'error', text: __('Failed to delete data.', 'trvlr') });
      }
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
         <div style={{ display: 'flex', gap: '10px' }}>
            <Button
               variant="secondary"
               isDestructive
               onClick={() => handleDelete(true)}
            >
               {__('Delete EVERYTHING (Inc. Images)', 'trvlr')}
            </Button>
            <Button
               variant="secondary"
               onClick={() => handleDelete(false)}
            >
               {__('Delete Posts Only (Keep Images)', 'trvlr')}
            </Button>
         </div>
      </div>
   );
};

