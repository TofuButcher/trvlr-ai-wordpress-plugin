import React from '@wordpress/element';
import { useState, useEffect, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice, Spinner } from '@wordpress/components';
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

export const CustomEditsForm = () => {
   const [customEdits, setCustomEdits] = useState<CustomEdit[]>([]);
   const [loading, setLoading] = useState(true);
   const [forceSyncSettings, setForceSyncSettings] = useState<Record<number, string[]>>({});
   const [saving, setSaving] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   useEffect(() => {
      loadCustomEdits();
   }, []);

   const loadCustomEdits = async () => {
      try {
         const edits = await apiFetch({ path: '/trvlr/v1/sync/custom-edits' }) as CustomEdit[];
         setCustomEdits(edits);

         const initial: Record<number, string[]> = {};
         edits.forEach((edit) => {
            initial[edit.id] = edit.force_sync_fields || [];
         });
         setForceSyncSettings(initial);
      } catch (error: any) {
         console.error('Error loading custom edits:', error);
         if (error?.data?.status === 403 || error?.code === 'rest_cookie_invalid_nonce') {
            setMessage({
               type: 'error',
               text: __('Authentication failed. Please refresh the page.', 'trvlr')
            });
         }
      } finally {
         setLoading(false);
      }
   };

   const toggleForceSync = (postId: number, fieldName: string) => {
      setForceSyncSettings(prev => {
         const current = prev[postId] || [];
         const updated = current.includes(fieldName)
            ? current.filter(f => f !== fieldName)
            : [...current, fieldName];
         return { ...prev, [postId]: updated };
      });
   };

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

   const handleSave = async () => {
      setSaving(true);
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

      setSaving(false);
   };

   const handleClear = async () => {
      if (!confirm(__('Clear all force sync settings?', 'trvlr'))) return;

      try {
         const response = await apiFetch({
            path: '/trvlr/v1/sync/clear-force-sync',
            method: 'POST',
         }) as { message: string };

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

   if (loading) {
      return <Spinner />;
   }

   if (customEdits.length === 0) {
      return null;
   }

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

         <div style={{ overflowX: 'auto' }}>
            <table className="wp-list-table widefat fixed striped">
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

         <div style={{ display: 'flex', gap: '10px' }}>
            <Button
               variant="primary"
               onClick={handleSave}
               isBusy={saving}
               disabled={saving}
            >
               {__('Save Force Sync Settings', 'trvlr')}
            </Button>
            <Button
               variant="secondary"
               onClick={handleClear}
            >
               {__('Clear All Force Sync Settings', 'trvlr')}
            </Button>
         </div>
      </div>
   );
};

