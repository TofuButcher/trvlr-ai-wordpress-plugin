import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   Card,
   Button,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useTrvlr } from '../context/TrvlrContext';

export const SystemStatus = () => {
   const { systemStatus, createPaymentPage } = useTrvlr();
   const [creating, setCreating] = useState(false);

   const handleCreatePaymentPage = async () => {
      setCreating(true);
      const result = await createPaymentPage();
      setCreating(false);

      if (result.success) {
         alert(__('Payment page created successfully!', 'trvlr'));
      } else {
         alert(__('Error creating payment page. Please try again.', 'trvlr'));
      }
   };

   const statusItemStyle = {
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      background: '#fff',
      padding: '10px 16px',
      border: '1px solid #ddd',
      borderRadius: '4px'
   };

   return (
      <div className="trvlr-system-status" style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
         <div style={statusItemStyle}>
            <div>
               <span className="dashicons dashicons-admin-page" style={{ marginRight: '8px' }}></span>
               <span>{__('Payment Confirmation Page', 'trvlr')}</span>
            </div>
            <div>
               {systemStatus.payment_page?.exists ? (
                  <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                     <Button
                        variant="primary"
                        size="small"
                        onClick={() => window.open(systemStatus.payment_page.url, '_blank')}
                     >
                        {__('View Page', 'trvlr')}
                     </Button>
                     <span className="trvlr-status-badge trvlr-status-success">
                        <span className="dashicons dashicons-yes-alt"></span>
                        {__('Active', 'trvlr')}
                     </span>
                  </div>
               ) : (
                  <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                     <Button
                        variant="primary"
                        size="small"
                        onClick={handleCreatePaymentPage}
                        isBusy={creating}
                        disabled={creating}
                     >
                        {__('Create Page', 'trvlr')}
                     </Button>
                     <span className="trvlr-status-badge trvlr-status-error">
                        <span className="dashicons dashicons-warning"></span>
                        {__('Not Found', 'trvlr')}
                     </span>
                  </div>
               )}
            </div>
         </div>

         <div style={statusItemStyle}>
            <div>
               <span className="dashicons dashicons-cloud" style={{ marginRight: '8px' }}></span>
               <span>{__('API Connection', 'trvlr')}</span>
            </div>
            <div>
               <span className="trvlr-status-badge trvlr-status-info">
                  <span className="dashicons dashicons-info"></span>
                  {__('Not Tested', 'trvlr')}
               </span>
            </div>
         </div>
      </div>
   )
}