import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Card } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';
import { PageHeading } from '../components/page-heading';
import { ManualSyncForm } from '../settings-forms/manual-sync-form';
import { ScheduleSettingsForm } from '../settings-forms/schedule-settings-form.tsx';
import { CustomEditsForm } from '../settings-forms/custom-edits-form';
import { DangerZoneForm } from '../settings-forms/danger-zone-form';

export const SyncSettings = () => {
   const { syncStats } = useTrvlr();

   const syncStatsElements = [

      {
         key: 'total_attractions',
         label: 'Total Attractions',
         color: '#1d2327',
      },
      {
         key: 'synced_count',
         label: 'Synced (No Edits)',
         color: '#00a32a',
      },
      {
         key: 'custom_edit_count',
         label: 'With Custom Edits',
         color: '#dba617',
      },
   ]

   return (
      <div className="trvlr-sync-settings">
         <PageHeading text={'Your TRVLR Products'} />
         <div className="trvlr-settings-section-spacer">
            <div style={{ display: 'grid', gap: '20px' }}>
               {/* Sync Statistics */}
               <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px' }}>
                  {syncStatsElements.map((element) => {
                     return (
                        <Card style={{ borderLeft: `5px solid ${element.color}` }}>
                           <div style={{ display: 'grid', gap: '10px', padding: '20px', textAlign: 'center' }}>
                              <div style={{ fontSize: '36px', fontWeight: 'bold', color: element.color }}>
                                 {syncStats?.[element.key] || 0}
                              </div>
                              <div style={{ color: '#666', fontSize: '14px' }}>
                                 {__(`${element.label}`, 'trvlr')}
                              </div>
                           </div>
                        </Card>
                     );
                  })}
               </div>
               {/* Manual Sync Button */}
               <ManualSyncForm />
            </div>

            <div>
               <PageHeading text={'Sync Settings'} />
               <ScheduleSettingsForm />
            </div>


            {/* Custom Edits Management */}
            <div style={{ display: 'grid', gap: '10px', justifyItems: 'start', width: '100%' }}>
               <PageHeading text={'Attractions with Custom Edits'} />
               <CustomEditsForm />
            </div>

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
         </div>
      </div >
   );
};

