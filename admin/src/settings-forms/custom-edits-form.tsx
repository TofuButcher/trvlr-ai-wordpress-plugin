import React from '@wordpress/element';
import { useState, useEffect, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Notice, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

interface CustomEdit {
   id: number;
   title: string;
   edit_url: string;
   modified: string;
   edited_fields: string[];
   edited_fields_labels: string[];
}

const asStringArray = (v: unknown): string[] => {
   if (Array.isArray(v)) {
      return v.filter((x): x is string => typeof x === 'string');
   }
   if (v && typeof v === 'object') {
      return Object.values(v as Record<string, string>).filter((x) => typeof x === 'string');
   }
   return [];
};

export const CustomEditsForm = () => {
   const [customEdits, setCustomEdits] = useState<CustomEdit[]>([]);
   const [loading, setLoading] = useState(true);
   const [busy, setBusy] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   useEffect(() => {
      loadCustomEdits();
   }, []);

   const loadCustomEdits = async () => {
      try {
         const raw = await apiFetch({ path: '/trvlr/v1/sync/custom-edits' }) as CustomEdit[];
         const edits = raw.map((edit) => ({
            ...edit,
            edited_fields: asStringArray(edit.edited_fields),
            edited_fields_labels: asStringArray(edit.edited_fields_labels),
         }));
         setCustomEdits(edits);
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

   const clearEdits = async (payload: { post_id?: number; fields?: string[] }, confirmText: string) => {
      if (!confirm(confirmText)) return;

      setBusy(true);
      setMessage(null);

      try {
         const response = await apiFetch({
            path: '/trvlr/v1/sync/clear-custom-edits',
            method: 'POST',
            data: payload,
         }) as { message: string };

         setMessage({ type: 'success', text: response.message });
         await loadCustomEdits();
      } catch (error) {
         setMessage({ type: 'error', text: __('Failed to clear custom edits.', 'trvlr') });
      }

      setBusy(false);
   };

   if (loading) {
      return <Spinner />;
   }

   if (customEdits.length === 0) {
      return (
         <p style={{ color: '#666', margin: 0 }}>
            {__('No attractions currently have Custom Edit fields.', 'trvlr')}
         </p>
      );
   }

   return (
      <div className="trvlr-settings-form">
         <p className="trvlr-settings-form-description" style={{ marginTop: 0 }}>
            {__(
               'Fields in Custom Edit mode are kept in WordPress and skipped on sync. Clearing a field returns it to Synced so the next sync can overwrite it from Traveloris.',
               'trvlr'
            )}
         </p>

         {message && (
            <Notice
               status={message.type}
               onRemove={() => setMessage(null)}
               isDismissible
            >
               {message.text}
            </Notice>
         )}

         <div style={{ overflowX: 'auto', width: '100%' }}>
            <table className="wp-list-table widefat fixed striped">
               <thead>
                  <tr>
                     <th style={{ width: '35%' }}>{__('Attraction', 'trvlr')}</th>
                     <th style={{ width: '30%' }}>{__('Custom Edit Fields', 'trvlr')}</th>
                     <th style={{ width: '15%' }}>{__('Last Modified', 'trvlr')}</th>
                     <th style={{ width: '20%' }}>{__('Actions', 'trvlr')}</th>
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
                              <div style={{ display: 'flex', flexWrap: 'wrap', gap: '6px' }}>
                                 {edit.edited_fields.map((field, index) => (
                                    <span
                                       key={field}
                                       style={{
                                          background: '#f0f0f1',
                                          padding: '4px 8px',
                                          borderRadius: '3px',
                                          fontSize: '12px',
                                          display: 'inline-flex',
                                          alignItems: 'center',
                                          gap: '6px',
                                       }}
                                    >
                                       {edit.edited_fields_labels[index] || field}
                                       <Button
                                          variant="link"
                                          isDestructive
                                          disabled={busy}
                                          onClick={() =>
                                             clearEdits(
                                                { post_id: edit.id, fields: [field] },
                                                __(
                                                   'Enable Traveloris sync for this field? The next sync will restore Traveloris content.',
                                                   'trvlr'
                                                )
                                             )
                                          }
                                          style={{ padding: 0, height: 'auto' }}
                                       >
                                          {__('Clear', 'trvlr')}
                                       </Button>
                                    </span>
                                 ))}
                              </div>
                           </td>
                           <td>{edit.modified}</td>
                           <td>
                              <Button
                                 variant="secondary"
                                 size="small"
                                 disabled={busy}
                                 onClick={() =>
                                    clearEdits(
                                       { post_id: edit.id },
                                       __(
                                          'Clear all Custom Edit fields for this attraction? The next sync will restore Traveloris content for those fields.',
                                          'trvlr'
                                       )
                                    )
                                 }
                              >
                                 {__('Clear All', 'trvlr')}
                              </Button>
                           </td>
                        </tr>
                     </Fragment>
                  ))}
               </tbody>
            </table>
         </div>

         <div style={{ display: 'flex', flexWrap: 'wrap', gap: '10px' }}>
            <Button
               variant="secondary"
               isDestructive
               onClick={() =>
                  clearEdits(
                     {},
                     __(
                        'Clear Custom Edit mode for every attraction on this site? The next sync will restore Traveloris content for those fields.',
                        'trvlr'
                     )
                  )
               }
               disabled={busy}
            >
               {__('Clear All Custom Edits (Sitewide)', 'trvlr')}
            </Button>
         </div>
      </div>
   );
};
