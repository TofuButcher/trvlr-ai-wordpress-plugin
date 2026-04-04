import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   TextControl,
   Button,
   Notice,
   ToggleControl,
} from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';

export const ConnectionSettingsForm = () => {
   const { connectionSettings, saveConnectionSettings, saving } = useTrvlr();
   const [saveStatus, setSaveStatus] = useState(null);

   const [organisationId, setOrganisationId] = useState(connectionSettings.organisation_id || '');
   const [disablePostType, setDisablePostType] = useState(!!connectionSettings.disable_attraction_post_type);
   const [disableSync, setDisableSync] = useState(!!connectionSettings.disable_attraction_sync);
   const [disableFrontendBooking, setDisableFrontendBooking] = useState(!!connectionSettings.disable_frontend_booking);

   useEffect(() => {
      setOrganisationId(connectionSettings.organisation_id || '');
      setDisablePostType(!!connectionSettings.disable_attraction_post_type);
      setDisableSync(!!connectionSettings.disable_attraction_sync);
      setDisableFrontendBooking(!!connectionSettings.disable_frontend_booking);
   }, [connectionSettings]);

   const onDisablePostTypeChange = (value) => {
      setDisablePostType(value);
      if (value) {
         setDisableSync(true);
      }
   };

   const handleSave = async () => {
      setSaveStatus(null);

      const payload = {
         organisation_id: organisationId,
         disable_attraction_post_type: disablePostType,
         disable_attraction_sync: disablePostType ? true : disableSync,
         disable_frontend_booking: disableFrontendBooking,
      };

      const result = await saveConnectionSettings(payload);

      if (result.success) {
         setSaveStatus('success');
         setTimeout(() => setSaveStatus(null), 3000);
      } else {
         console.error('Error saving connection settings:', result.error);
         setSaveStatus('error');
      }
   };

   const syncToggleChecked = disablePostType ? true : disableSync;
   const syncToggleDisabled = disablePostType;

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

         <div className="trvlr-connection-features" style={{ marginTop: '1.5rem' }}>
            <h3 className="trvlr-settings-heading" style={{ marginBottom: '0.25rem' }}>
               {__('TRVLR features', 'trvlr')}
            </h3>
            <p style={{ marginTop: 0, marginBottom: '1rem', color: '#646970', fontSize: '13px' }}>
               {__('Turn off what you don’t need', 'trvlr')}
            </p>

            <ToggleControl
               label={__('Disable TRVLR Attraction post type', 'trvlr')}
               help={__(
                  'When enabled, the plugin does not register the trvlr_attraction post type. Use your own post types or content; syncing from TRVLR is turned off while this is enabled.',
                  'trvlr'
               )}
               checked={disablePostType}
               onChange={onDisablePostTypeChange}
            />

            <ToggleControl
               label={__('Disable syncing attractions from TRVLR', 'trvlr')}
               help={__(
                  'When enabled, scheduled and manual catalog sync do not run. Per-attraction sync controls are unavailable.',
                  'trvlr'
               )}
               checked={syncToggleChecked}
               disabled={syncToggleDisabled}
               onChange={(v) => !syncToggleDisabled && setDisableSync(v)}
            />
            {syncToggleDisabled && (
               <p style={{ margin: '-8px 0 12px', paddingLeft: '48px', fontSize: '12px', color: '#646970' }}>
                  {__('Unavailable while the TRVLR Attraction post type is disabled.', 'trvlr')}
               </p>
            )}

            <ToggleControl
               label={__('Disable frontend booking (scripts & modals)', 'trvlr')}
               help={__(
                  'Stops loading the booking modal, checkout embed, and related scripts. You can still use shortcodes and templates for display if you build a custom booking flow.',
                  'trvlr'
               )}
               checked={disableFrontendBooking}
               onChange={setDisableFrontendBooking}
            />
         </div>

         <Button
            variant="primary"
            onClick={handleSave}
            isBusy={saving}
            disabled={saving}
            style={{ marginTop: '1rem' }}
         >
            {saving ? __('Saving...', 'trvlr') : __('Save Settings', 'trvlr')}
         </Button>
      </div>
   );
};
