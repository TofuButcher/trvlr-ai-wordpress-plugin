import React, { ColorPicker, RangeControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ThemeFieldConfig } from '../config/themeConfig';

interface ThemeFieldProps {
   field: ThemeFieldConfig;
   value: string | number;
   onChange: (value: string | number) => void;
}

export const ThemeField: React.FC<ThemeFieldProps> = ({ field, value, onChange }) => {
   switch (field.type) {
      case 'color':
         return (
            <div style={{ marginBottom: '20px' }}>
               <strong>{field.label}</strong>
               {field.description && (
                  <p style={{ fontSize: '12px', color: '#666', margin: '4px 0 8px' }}>
                     {field.description}
                  </p>
               )}
               <ColorPicker
                  color={value as string}
                  onChangeComplete={(color) => {
                     // Handle different color picker outputs
                     const colorValue = color.hex || `rgba(${color.rgb.r},${color.rgb.g},${color.rgb.b},${color.rgb.a})`;
                     onChange(colorValue);
                  }}
               />
            </div>
         );

      case 'range':
         return (
            <RangeControl
               label={field.label}
               value={value as number}
               onChange={(val) => onChange(val ?? field.default)}
               min={field.min}
               max={field.max}
               step={field.step}
               help={field.description}
            />
         );

      case 'text':
         return (
            <TextControl
               label={field.label}
               value={value as string}
               onChange={onChange}
               help={field.description}
            />
         );

      default:
         return null;
   }
};

