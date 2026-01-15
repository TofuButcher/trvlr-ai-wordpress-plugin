import React from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { Panel, PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useTrvlr, getAllFieldsFromConfig, getThemeDefaults, mergeWithDefaults } from '../context/TrvlrContext';
import { PageHeading } from '../components/page-heading';
import { ThemeField } from '../components/theme-field';
import { AttractionCardPreview } from '../components/theme-preview';

export const ThemeSettings = () => {
   const { themeSettings, saveThemeSettings, saving, themeConfig, processedThemeConfig } = useTrvlr();

   // Initialize state with merged defaults + saved settings
   const [settings, setSettings] = useState(() =>
      mergeWithDefaults(themeSettings, themeConfig)
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
         setSettings(getThemeDefaults(themeConfig));
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

      const allFields = getAllFieldsFromConfig(themeConfig);
      allFields.forEach(field => {
         if (field.cssVar) {
            const value = settings[field.key];
            const unit = field.unit || '';
            preview.style.setProperty(field.cssVar, `${value}${unit}`);
         }
      });
   };

   // Render a field or group
   const renderFieldOrGroup = (item: any) => {
      if (item.type === 'group') {
         // Render a cols-X wrapper
         return (
            <div key={item.colsClass} className={`trvlr-${item.colsClass}`}>
               {item.label && (
                  <div style={{ gridColumn: '1 / -1', marginBottom: '8px' }}>
                     <strong>{item.label}</strong>
                     {item.description && (
                        <p style={{ margin: '4px 0 0', color: '#666', fontSize: '13px' }}>
                           {item.description}
                        </p>
                     )}
                  </div>
               )}
               {item.fields.map((field: any) => (
                  <ThemeField
                     key={field.key}
                     field={field}
                     value={settings[field.key]}
                     onChange={(value) => updateField(field.key, value)}
                  />
               ))}
            </div>
         );
      } else {
         // Regular field
         return (
            <ThemeField
               key={item.key}
               field={item}
               value={settings[item.key]}
               onChange={(value) => updateField(item.key, value)}
            />
         );
      }
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
                  {Object.entries(processedThemeConfig).map(([groupKey, group]: [string, any]) => (
                     <PanelBody
                        key={groupKey}
                        title={group.label}
                        initialOpen={false}
                     >
                        {group.description && (
                           <p style={{ marginTop: 0, color: '#666', fontSize: '13px' }}>
                              {group.description}
                           </p>
                        )}

                        {/* Render fields and groups */}
                        <div>
                           {group.fields.map((item: any) => renderFieldOrGroup(item))}
                        </div>
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

