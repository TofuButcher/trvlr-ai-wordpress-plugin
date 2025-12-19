import React from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { Panel, PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useTrvlr } from '../context/TrvlrContext';
import { PageHeading } from '../components/page-heading';
import { ThemeField } from '../components/theme-field';
import { AttractionCardPreview } from '../components/theme-preview';
import { themeConfig, getThemeDefaults, mergeWithDefaults, getAllFields } from '../config/themeConfig';

export const ThemeSettings = () => {
   const { themeSettings, saveThemeSettings, saving } = useTrvlr();

   // Initialize state with merged defaults + saved settings
   const [settings, setSettings] = useState(() =>
      mergeWithDefaults(themeSettings)
   );

   // Update a single field
   const updateField = (key: string, value: string | number) => {
      setSettings(prev => ({
         ...prev,
         [key]: value,
      }));
   };

   // Reset to defaults
   const resetToDefaults = () => {
      if (confirm(__('Reset all theme settings to defaults?', 'trvlr'))) {
         setSettings(getThemeDefaults());
      }
   };

   // Save settings
   const handleSave = async () => {
      const result = await saveThemeSettings(settings);

      if (result.success) {
         alert(__('Theme settings saved successfully!', 'trvlr'));
      } else {
         console.error('Error saving settings:', result.error);
         alert(__('Error saving settings. Please try again.', 'trvlr'));
      }
   };

   // Apply CSS variables to preview in real-time
   useEffect(() => {
      applyCSSVariables();
   }, [settings]);

   const applyCSSVariables = () => {
      const preview = document.getElementById('trvlr-preview-card');
      if (!preview) return;

      getAllFields().forEach(field => {
         const value = settings[field.key];
         const unit = field.unit || '';
         preview.style.setProperty(field.cssVar, `${value}${unit}`);
      });
   };

   return (
      <div className="trvlr-theme-settings trvlr-settings-sidebar-wrap">
         <div style={{ display: 'flex', flexDirection: 'column' }}>
            <PageHeading
               text="Customise appearance of components"
            />
            {/* Settings Panels */}
            <div>
               <Panel>
                  {themeConfig.map(group => (
                     <PanelBody
                        key={group.key}
                        title={group.label}
                        initialOpen={false}
                     >
                        {group.description && (
                           <p style={{ marginTop: 0, color: '#666', fontSize: '13px' }}>
                              {group.description}
                           </p>
                        )}

                        {/* Auto-generate fields based on type */}
                        {group.key === 'colors' ? (
                           // Grid layout for colors
                           <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px' }}>
                              {group.fields.map(field => (
                                 <ThemeField
                                    key={field.key}
                                    field={field}
                                    value={settings[field.key]}
                                    onChange={(value) => updateField(field.key, value)}
                                 />
                              ))}
                           </div>
                        ) : (
                           // Stack layout for other fields
                           <div>
                              {group.fields.map(field => (
                                 <ThemeField
                                    key={field.key}
                                    field={field}
                                    value={settings[field.key]}
                                    onChange={(value) => updateField(field.key, value)}
                                 />
                              ))}
                           </div>
                        )}
                     </PanelBody>
                  ))}
               </Panel>
            </div>
            <div style={{ display: 'flex', gap: '10px', marginTop: '20px', flexGrow: 1, alignItems: 'flex-end' }}>
               <Button
                  variant="primary"
                  onClick={handleSave}
                  isBusy={saving}
                  disabled={saving}
               >
                  {saving ? __('Saving...', 'trvlr') : __('Save Settings', 'trvlr')}
               </Button>
               <Button
                  variant="secondary"
                  onClick={resetToDefaults}
                  disabled={saving}
               >
                  {__('Reset to Defaults', 'trvlr')}
               </Button>
            </div>
         </div>

         {/* Live Preview (Sticky) */}
         <div>
            <PageHeading
               text="Preview"
            />
            <div id="trvlr-preview-card" style={{ display: 'flex', position: 'sticky', top: '50px' }}>
               <AttractionCardPreview />
            </div>
         </div>
      </div>
   );
};

