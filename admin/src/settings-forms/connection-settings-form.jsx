import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   TextControl,
   Button,
   Notice,
} from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const ConnectionSettingsForm = () => {
   const { connectionSettings, saveConnectionSettings, saving } = useTrvlr();
   const [saveStatus, setSaveStatus] = useState(null);

   const [organisationId, setOrganisationId] = useState(connectionSettings.organisation_id || '');

   const handleSave = async () => {
      setSaveStatus(null);

      const result = await saveConnectionSettings({
         organisation_id: organisationId,
      });

      if (result.success) {
         setSaveStatus('success');
         setTimeout(() => setSaveStatus(null), 3000);
      } else {
         console.error('Error saving connection settings:', result.error);
         setSaveStatus('error');
      }
   };

   return (
      <div id="trvlr-connection-settings-form" className="trvlr-settings-form">
         {saveStatus === 'success' && (
            <Notice status="success" isDismissible={false}>
               {__('Settings saved successfully!', 'trvlr')}
            </Notice>
         )}
         {saveStatus === 'error' && (
            <Notice status="error" isDismissible={false}>
               {__('Error saving settings. Please try again.', 'trvlr')}
            </Notice>
         )}
         <TextControl
            label={__('Organisation ID', 'trvlr')}
            value={organisationId}
            onChange={setOrganisationId}
            help={__('Your Organisation ID from TRVLR AI.', 'trvlr')}
         />
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
