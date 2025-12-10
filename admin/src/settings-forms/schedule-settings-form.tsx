import React from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, SelectControl, ToggleControl, Notice } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const ScheduleSettingsForm = () => {
   const { scheduleSettings, saveScheduleSettings } = useTrvlr();
   const [scheduleEnabled, setScheduleEnabled] = useState(scheduleSettings?.enabled || false);
   const [scheduleFrequency, setScheduleFrequency] = useState(scheduleSettings?.frequency || 'daily');
   const [nextSync, setNextSync] = useState(scheduleSettings?.next_sync || null);
   const [saving, setSaving] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

   useEffect(() => {
      setScheduleEnabled(scheduleSettings?.enabled || false);
      setScheduleFrequency(scheduleSettings?.frequency || 'daily');
      setNextSync(scheduleSettings?.next_sync || null);
   }, [scheduleSettings]);

   const handleSave = async () => {
      setSaving(true);
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

      setSaving(false);
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

         <ToggleControl
            label={__('Enable automatic synchronization', 'trvlr')}
            checked={scheduleEnabled}
            onChange={setScheduleEnabled}
         />

         {scheduleEnabled && nextSync && (
            <div style={{ marginTop: '20px', marginBottom: '15px', padding: '10px', background: '#f0f0f1', borderRadius: '4px' }}>
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
            onClick={handleSave}
            isBusy={saving}
            disabled={saving}
         >
            {__('Save Schedule Settings', 'trvlr')}
         </Button>
      </div>
   );
};
