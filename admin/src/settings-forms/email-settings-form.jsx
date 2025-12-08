import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   TextControl,
   CheckboxControl,
   Button,
   Notice,
} from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const EmailSettingsForm = () => {
   const { notificationSettings, saveNotificationSettings, saving } = useTrvlr();
   const [saveStatus, setSaveStatus] = useState(null);

   const [notificationEmail, setNotificationEmail] = useState(notificationSettings.email || '');
   const [notifyErrors, setNotifyErrors] = useState(notificationSettings.notify_errors ?? true);
   const [notifyComplete, setNotifyComplete] = useState(notificationSettings.notify_complete ?? false);
   const [notifyWeekly, setNotifyWeekly] = useState(notificationSettings.notify_weekly ?? false);

   const handleSave = async () => {
      setSaveStatus(null);

      const result = await saveNotificationSettings({
         email: notificationEmail,
         notify_errors: notifyErrors,
         notify_complete: notifyComplete,
         notify_weekly: notifyWeekly,
      });

      if (result.success) {
         console.log('Save response:', result.data);
         setSaveStatus('success');
         setTimeout(() => setSaveStatus(null), 3000);
      } else {
         console.error('Error saving email settings:', result.error);
         setSaveStatus('error');
      }
   };

   return (
      <div id="trvlr-email-settings-form" className="trvlr-settings-form">
         {saveStatus === 'success' && (
            <Notice status="success" isDismissible={false} className="w-full">
               {__('Settings saved successfully!', 'trvlr')}
            </Notice>
         )}
         {saveStatus === 'error' && (
            <Notice status="error" isDismissible={false} className="w-full">
               {__('Error saving email settings. Please try again.', 'trvlr')}
            </Notice>
         )}

         <TextControl
            label={__('Notification Email', 'trvlr')}
            value={notificationEmail}
            onChange={setNotificationEmail}
            help={__('Email address for sync notifications.', 'trvlr')}
            type="email"
         />
         <div className="trvlr-settings-checkbox-group">
            <CheckboxControl
               checked={notifyErrors}
               label={__('Notify on sync errors', 'trvlr')}
               onChange={(value) => setNotifyErrors(value)}
            />
            <CheckboxControl
               checked={notifyComplete}
               label={__('Notify on sync completion', 'trvlr')}
               onChange={(value) => setNotifyComplete(value)}
            />
            <CheckboxControl
               checked={notifyWeekly}
               label={__('Send weekly summary', 'trvlr')}
               onChange={(value) => setNotifyWeekly(value)}
            />
         </div>
         <Button
            variant="primary"
            onClick={handleSave}
            isBusy={saving}
            disabled={saving}
         >
            {saving ? __('Saving...', 'trvlr') : __('Save Settings', 'trvlr')}
         </Button>
      </div >
   );
}
