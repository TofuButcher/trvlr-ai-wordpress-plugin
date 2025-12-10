import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Card } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';
import { PageHeader } from '../components/page-header';
import { ManualSyncForm } from '../settings-forms/manual-sync-form';
import { ScheduleSettingsForm } from '../settings-forms/schedule-settings-form.tsx';
import { CustomEditsForm } from '../settings-forms/custom-edits-form';
import { DangerZoneForm } from '../settings-forms/danger-zone-form';

export const SyncSettings = () => {
   const { syncStats } = useTrvlr();

   return (
      <div className="trvlr-sync-settings">
         <PageHeader
            title={__('Sync Management', 'trvlr')}
            description={__('Manage data synchronization with the TRVLR AI system.', 'trvlr')}
         />

         <div className="trvlr-settings-section-spacer">
            {/* Sync Statistics */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px' }}>
               <Card style={{ borderLeft: '5px solid #1d2327' }}>
                  <div style={{ display: 'grid', gap: '10px', padding: '20px', textAlign: 'center' }}>
                     <div style={{ fontSize: '36px', fontWeight: 'bold', color: '#1d2327' }}>
                        {syncStats?.total_attractions || 0}
                     </div>
                     <div style={{ color: '#666', fontSize: '14px' }}>
                        {__('Total Attractions', 'trvlr')}
                     </div>
                  </div>
               </Card>
               <Card style={{ borderLeft: '5px solid #00a32a' }}>
                  <div style={{ display: 'grid', gap: '10px', padding: '20px', textAlign: 'center' }}>
                     <div style={{ fontSize: '36px', fontWeight: 'bold', color: '#00a32a' }}>
                        {syncStats?.synced_count || 0}
                     </div>
                     <div style={{ color: '#666', fontSize: '14px' }}>
                        {__('Synced (No Edits)', 'trvlr')}
                     </div>
                  </div>
               </Card>
               <Card style={{ borderLeft: '5px solid #dba617' }}>
                  <div style={{ display: 'grid', gap: '10px', padding: '20px', textAlign: 'center' }}>
                     <div style={{ fontSize: '36px', fontWeight: 'bold', color: '#dba617' }}>
                        {syncStats?.custom_edit_count || 0}
                     </div>
                     <div style={{ color: '#666', fontSize: '14px' }}>
                        {__('With Custom Edits', 'trvlr')}
                     </div>
                  </div>
               </Card>
            </div>

            {/* Manual Sync */}
            <Card>
               <div style={{ padding: '20px' }}>
                  <h3 className="trvlr-settings-form-heading">{__('Manual Sync', 'trvlr')}</h3>
                  <p className="trvlr-settings-form-description">
                     {__('Manually trigger a sync with the TRVLR AI system.', 'trvlr')}
                  </p>
                  <ManualSyncForm />
               </div>
            </Card>

            {/* Scheduled Sync */}
            <Card>
               <div style={{ padding: '20px' }}>
                  <h3 className="trvlr-settings-form-heading">{__('Scheduled Sync', 'trvlr')}</h3>
                  <p className="trvlr-settings-form-description">
                     {__('Configure automatic synchronization schedule.', 'trvlr')}
                  </p>
                  <ScheduleSettingsForm />
               </div>
            </Card>

            {/* Custom Edits Management */}
            <Card>
               <div style={{ padding: '20px' }}>
                  <h3 className="trvlr-settings-form-heading">{__('Attractions with Custom Edits', 'trvlr')}</h3>
                  <p className="trvlr-settings-form-description">
                     {__('These attractions have been manually edited in WordPress. Select which fields to overwrite with TRVLR data on the next sync.', 'trvlr')}
                  </p>
                  <CustomEditsForm />
               </div>
            </Card>

            {/* Danger Zone */}
            <Card style={{ borderColor: '#d63638' }}>
               <div style={{ padding: '20px' }}>
                  <h3 className="trvlr-settings-form-heading" >{__('Danger Zone', 'trvlr')}</h3>
                  <p className="trvlr-settings-form-description">
                     {__('Delete data imported by this plugin.', 'trvlr')}
                  </p>
                  <DangerZoneForm />
               </div>
            </Card>
         </div >
      </div >
   );
};

