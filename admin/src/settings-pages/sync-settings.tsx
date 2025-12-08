import React from '@wordpress/element';
import { useState, useEffect, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Card, SelectControl, ToggleControl, Notice } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';
import { PageHeader } from '../components/page-header';
import apiFetch from '@wordpress/api-fetch';

declare global {
   interface Window {
      trvlrInitialData?: {
         restNonce?: string;
         [key: string]: any;
      };
      wp?: any;
   }
}

interface CustomEdit {
   id: number;
   title: string;
   edit_url: string;
   modified: string;
   edited_fields: string[];
   force_sync_fields: string[];
   edited_fields_labels: string[];
   force_sync_fields_labels: string[];
}

export const SyncSettings = () => {
   const {
      syncStats,
      scheduleSettings,
      refreshSyncStats,
      triggerManualSync,
      saveScheduleSettings,
      deleteData,
      refreshing,
   } = useTrvlr();

   // Schedule state
   const [scheduleEnabled, setScheduleEnabled] = useState(scheduleSettings?.enabled || false);
   const [scheduleFrequency, setScheduleFrequency] = useState(scheduleSettings?.frequency || 'daily');
   const [nextSync, setNextSync] = useState(scheduleSettings?.next_sync || null);

   // Custom edits state
   const [customEdits, setCustomEdits] = useState<CustomEdit[]>([]);
   const [loadingEdits, setLoadingEdits] = useState(true);
   const [forceSyncSettings, setForceSyncSettings] = useState<Record<number, string[]>>({});

   // UI state
   const [syncing, setSyncing] = useState(false);
   const [savingSchedule, setSavingSchedule] = useState(false);
   const [savingForceSync, setSavingForceSync] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   // Load custom edits
   useEffect(() => {
      console.log('TRVLR Sync: Component mounted, checking auth...');
      console.log('TRVLR Initial Data:', window.trvlrInitialData);

      // Only load if we have proper authentication
      if (window.trvlrInitialData?.restNonce) {
         loadCustomEdits();
      } else {
         console.error('TRVLR Sync: No REST nonce available!');
         setMessage({
            type: 'error',
            text: __('Authentication not configured. Please refresh the page.', 'trvlr')
         });
         setLoadingEdits(false);
      }
   }, []);

   const loadCustomEdits = async () => {
      try {
         console.log('TRVLR Sync: Fetching custom edits...');
         const edits = await apiFetch({ path: '/trvlr/v1/sync/custom-edits' }) as CustomEdit[];
         console.log('TRVLR Sync: Loaded', edits.length, 'custom edits');
         setCustomEdits(edits);

         // Initialize force sync settings
         const initial: Record<number, string[]> = {};
         edits.forEach((edit) => {
            initial[edit.id] = edit.force_sync_fields || [];
         });
         setForceSyncSettings(initial);
      } catch (error: any) {
         console.error('TRVLR Sync: Error loading custom edits:', error);

         // Check if it's a 403 authentication error
         if (error?.data?.status === 403 || error?.code === 'rest_cookie_invalid_nonce') {
            setMessage({
               type: 'error',
               text: __('Authentication failed. Please refresh the page.', 'trvlr')
            });
         }
      } finally {
         setLoadingEdits(false);
      }
   };

   // Handle manual sync
   const handleManualSync = async () => {
      setSyncing(true);
      setMessage(null);

      const result = await triggerManualSync();

      if (result.success) {
         setMessage({ type: 'success', text: __('Sync completed successfully!', 'trvlr') });
         await loadCustomEdits(); // Reload custom edits after sync
      } else {
         setMessage({ type: 'error', text: __('Sync failed. Please check logs.', 'trvlr') });
      }

      setSyncing(false);
   };

   // Handle schedule save
   const handleSaveSchedule = async () => {
      setSavingSchedule(true);
      setMessage(null);

      const result = await saveScheduleSettings({
         enabled: scheduleEnabled,
         frequency: scheduleFrequency,
      });

      if (result.success) {
         setNextSync(result.data?.next_sync || null);
         setMessage({ type: 'success', text: __('Schedule settings saved!', 'trvlr') });
      } else {
         setMessage({ type: 'error', text: __('Failed to save schedule settings.', 'trvlr') });
      }

      setSavingSchedule(false);
   };

   // Handle force sync toggle for a field
   const toggleForceSync = (postId: number, fieldName: string) => {
      setForceSyncSettings(prev => {
         const current = prev[postId] || [];
         const updated = current.includes(fieldName)
            ? current.filter(f => f !== fieldName)
            : [...current, fieldName];
         return { ...prev, [postId]: updated };
      });
   };

   // Handle select all fields for a post
   const toggleSelectAll = (postId: number, allFields: string[]) => {
      setForceSyncSettings(prev => {
         const current = prev[postId] || [];
         const allSelected = allFields.every(f => current.includes(f));
         return {
            ...prev,
            [postId]: allSelected ? [] : allFields,
         };
      });
   };

   // Save force sync settings
   const handleSaveForceSync = async () => {
      setSavingForceSync(true);
      setMessage(null);

      try {
         const response = await apiFetch({
            path: '/trvlr/v1/sync/force-sync',
            method: 'POST',
            data: { force_sync_fields: forceSyncSettings },
         }) as { message: string };

         setMessage({ type: 'success', text: response.message });
      } catch (error) {
         setMessage({ type: 'error', text: __('Failed to save force sync settings.', 'trvlr') });
      }

      setSavingForceSync(false);
   };

   // Clear all force sync settings
   const handleClearForceSync = async () => {
      if (!confirm(__('Clear all force sync settings?', 'trvlr'))) return;

      try {
         const response = await apiFetch({
            path: '/trvlr/v1/sync/clear-force-sync',
            method: 'POST',
         }) as { message: string };

         // Reset local state
         const cleared: Record<number, string[]> = {};
         customEdits.forEach((edit) => {
            cleared[edit.id] = [];
         });
         setForceSyncSettings(cleared);

         setMessage({ type: 'success', text: response.message });
      } catch (error) {
         setMessage({ type: 'error', text: __('Failed to clear force sync settings.', 'trvlr') });
      }
   };

   // Handle delete data
   const handleDelete = async (includeMedia: boolean) => {
      const confirmText = includeMedia
         ? __('Delete ALL data including images? This cannot be undone!', 'trvlr')
         : __('Delete all attraction posts? (Images will be kept)', 'trvlr');

      if (!confirm(confirmText)) return;

      const result = await deleteData(includeMedia);

      if (result.success) {
         setMessage({ type: 'success', text: __('Data deleted successfully.', 'trvlr') });
         await loadCustomEdits();
      } else {
         setMessage({ type: 'error', text: __('Failed to delete data.', 'trvlr') });
      }
   };

   return (
      <div className="trvlr-sync-settings">
         <PageHeader
            title={__('Sync Management', 'trvlr')}
            description={__('Manage data synchronization with the TRVLR AI system.', 'trvlr')}
         />

         {message && (
            <Notice
               status={message.type}
               onRemove={() => setMessage(null)}
               isDismissible
            >
               {message.text}
            </Notice>
         )}

         {/* Sync Statistics */}
         <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px', marginBottom: '30px' }}>
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
         <Card style={{ marginBottom: '20px' }}>
            <div style={{ padding: '20px' }}>
               <h3 style={{ marginTop: 0 }}>{__('Manual Sync', 'trvlr')}</h3>
               <p style={{ color: '#666' }}>
                  {__('Manually trigger a sync with the TRVLR AI system.', 'trvlr')}
               </p>
               <Button
                  variant="primary"
                  onClick={handleManualSync}
                  isBusy={syncing}
                  disabled={syncing}
               >
                  {syncing ? __('Syncing...', 'trvlr') : __('Sync Now', 'trvlr')}
               </Button>
            </div>
         </Card>

         {/* Scheduled Sync */}
         <Card style={{ marginBottom: '20px' }}>
            <div style={{ padding: '20px' }}>
               <h3 style={{ marginTop: 0 }}>{__('Scheduled Sync', 'trvlr')}</h3>
               <p style={{ color: '#666' }}>
                  {__('Configure automatic synchronization schedule.', 'trvlr')}
               </p>

               <ToggleControl
                  label={__('Enable automatic synchronization', 'trvlr')}
                  checked={scheduleEnabled}
                  onChange={setScheduleEnabled}
               />

               {scheduleEnabled && nextSync && (
                  <div style={{ marginTop: '10px', padding: '10px', background: '#f0f0f1', borderRadius: '4px' }}>
                     <strong>{__('Next sync scheduled for:', 'trvlr')}</strong> {nextSync}
                  </div>
               )}

               <SelectControl
                  label={__('Sync Frequency', 'trvlr')}
                  value={scheduleFrequency}
                  onChange={setScheduleFrequency}
                  disabled={!scheduleEnabled}
                  options={[
                     { label: __('Hourly', 'trvlr'), value: 'hourly' },
                     { label: __('Twice Daily', 'trvlr'), value: 'twicedaily' },
                     { label: __('Daily', 'trvlr'), value: 'daily' },
                     { label: __('Weekly', 'trvlr'), value: 'weekly' },
                  ]}
                  help={__('How often should attractions be synced automatically?', 'trvlr')}
               />

               <Button
                  variant="primary"
                  onClick={handleSaveSchedule}
                  isBusy={savingSchedule}
                  disabled={savingSchedule}
               >
                  {__('Save Schedule Settings', 'trvlr')}
               </Button>
            </div>
         </Card>

         {/* Custom Edits Management */}
         {customEdits.length > 0 && (
            <Card style={{ marginBottom: '20px' }}>
               <div style={{ padding: '20px' }}>
                  <h3 style={{ marginTop: 0 }}>{__('Attractions with Custom Edits', 'trvlr')}</h3>
                  <p style={{ color: '#666' }}>
                     {__('These attractions have been manually edited in WordPress. Select which fields to overwrite with TRVLR data on the next sync.', 'trvlr')}
                  </p>

                  <div style={{ overflowX: 'auto' }}>
                     <table className="wp-list-table widefat fixed striped" style={{ marginTop: '15px' }}>
                        <thead>
                           <tr>
                              <th style={{ width: '35%' }}>{__('Attraction', 'trvlr')}</th>
                              <th style={{ width: '25%' }}>{__('Edited Fields', 'trvlr')}</th>
                              <th style={{ width: '15%' }}>{__('Last Modified', 'trvlr')}</th>
                              <th style={{ width: '25%' }}>{__('Force Sync Fields', 'trvlr')}</th>
                           </tr>
                        </thead>
                        <tbody>
                           {customEdits.map((edit) => (
                              <Fragment key={edit.id}>
                                 <tr>
                                    <td>
                                       <strong>
                                          <a href={edit.edit_url} target="_blank" rel="noopener noreferrer">
                                             {edit.title}
                                          </a>
                                       </strong>
                                    </td>
                                    <td>
                                       <span style={{ background: '#f0f0f1', padding: '4px 8px', borderRadius: '3px', fontSize: '12px' }}>
                                          {edit.edited_fields_labels?.join(', ')}
                                       </span>
                                    </td>
                                    <td>{edit.modified}</td>
                                    <td>
                                       {forceSyncSettings[edit.id]?.length > 0 && (
                                          <div style={{ marginBottom: '8px', fontSize: '12px' }}>
                                             <span className="dashicons dashicons-yes-alt" style={{ color: '#00a32a' }}></span>
                                             <strong>{__('Will overwrite:', 'trvlr')}</strong> {forceSyncSettings[edit.id].map(f =>
                                                edit.edited_fields_labels[edit.edited_fields.indexOf(f)]
                                             ).join(', ')}
                                          </div>
                                       )}
                                       <Button
                                          variant="secondary"
                                          size="small"
                                          onClick={() => {
                                             const row = document.getElementById(`fields-${edit.id}`);
                                             if (row) {
                                                row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
                                             }
                                          }}
                                       >
                                          <span className="dashicons dashicons-arrow-down-alt2" style={{ fontSize: '16px' }}></span>
                                          {__('Select Fields', 'trvlr')}
                                       </Button>
                                    </td>
                                 </tr>
                                 <tr id={`fields-${edit.id}`} style={{ display: 'none' }}>
                                    <td colSpan={4} style={{ background: '#f9f9f9', padding: '15px' }}>
                                       <div>
                                          <label style={{ fontWeight: 'bold', marginRight: '20px', display: 'block', marginBottom: '10px' }}>
                                             <input
                                                type="checkbox"
                                                checked={edit.edited_fields.every(f => forceSyncSettings[edit.id]?.includes(f))}
                                                onChange={() => toggleSelectAll(edit.id, edit.edited_fields)}
                                                style={{ marginRight: '5px' }}
                                             />
                                             {__('Select All Fields', 'trvlr')}
                                          </label>
                                          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: '10px' }}>
                                             {edit.edited_fields.map((field, index) => (
                                                <label key={field} style={{ display: 'flex', alignItems: 'center' }}>
                                                   <input
                                                      type="checkbox"
                                                      checked={forceSyncSettings[edit.id]?.includes(field) || false}
                                                      onChange={() => toggleForceSync(edit.id, field)}
                                                      style={{ marginRight: '5px' }}
                                                   />
                                                   {edit.edited_fields_labels[index]}
                                                </label>
                                             ))}
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                              </Fragment>
                           ))}
                        </tbody>
                     </table>
                  </div>

                  <div style={{ marginTop: '15px', display: 'flex', gap: '10px' }}>
                     <Button
                        variant="primary"
                        onClick={handleSaveForceSync}
                        isBusy={savingForceSync}
                        disabled={savingForceSync}
                     >
                        {__('Save Force Sync Settings', 'trvlr')}
                     </Button>
                     <Button
                        variant="secondary"
                        onClick={handleClearForceSync}
                     >
                        {__('Clear All Force Sync Settings', 'trvlr')}
                     </Button>
                  </div>
               </div>
            </Card>
         )}

         {/* Danger Zone */}
         <Card style={{ borderColor: '#d63638' }}>
            <div style={{ padding: '20px' }}>
               <h3 style={{ marginTop: 0, color: '#d63638' }}>{__('Danger Zone', 'trvlr')}</h3>
               <p style={{ color: '#666' }}>
                  {__('Delete data imported by this plugin.', 'trvlr')}
               </p>
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
         </Card>
      </div>
   );
};

