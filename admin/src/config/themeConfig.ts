/**
 * Theme Configuration
 * 
 * Single source of truth for all theme customization options.
 * This config is used to:
 * - Auto-generate form fields in React
 * - Provide default values
 * - Output CSS variables
 * - Validate settings
 */

export interface ThemeFieldConfig {
   key: string;
   label: string;
   type: 'color' | 'range' | 'text' | 'select';
   default: string | number;
   cssVar: string;
   description?: string;
   min?: number;
   max?: number;
   step?: number;
   unit?: string;
   options?: Array<{ label: string; value: string | number }>;
}

export interface ThemeGroupConfig {
   key: string;
   label: string;
   description?: string;
   fields: ThemeFieldConfig[];
}

export const themeConfig: ThemeGroupConfig[] = [
   {
      key: 'colors',
      label: 'Colors',
      description: 'Global color scheme for TRVLR components',
      fields: [
         {
            key: 'primaryColor',
            label: 'Primary Color',
            type: 'color',
            default: 'hsl(245, 90%, 50%)',
            cssVar: '--trvlr-primary-color',
            description: 'Main brand color used for buttons and links',
         },
         {
            key: 'primaryActiveColor',
            label: 'Primary Hover Color',
            type: 'color',
            default: 'hsl(245, 100%, 40%)',
            cssVar: '--trvlr-primary-active-color',
            description: 'Color when hovering over primary elements',
         },
         {
            key: 'accentColor',
            label: 'Accent Color',
            type: 'color',
            default: 'hsl(57, 100%, 50%)',
            cssVar: '--trvlr-accent-color',
            description: 'Secondary accent color for highlights',
         },
         {
            key: 'headingColor',
            label: 'Heading Color',
            type: 'color',
            default: 'hsl(0, 0%, 0%)',
            cssVar: '--trvlr-heading-color',
            description: 'Color for headings and titles',
         },
         {
            key: 'textMutedColor',
            label: 'Text Muted',
            type: 'color',
            default: 'hsl(0, 0%, 40%)',
            cssVar: '--trvlr-text-muted-color',
            description: 'Color for secondary text',
         },
      ],
   },
   {
      key: 'attractionCards',
      label: 'Attraction Cards',
      description: 'Styling for attraction cards',
      fields: [
         {
            key: 'cardBackground',
            label: 'Card Background',
            type: 'color',
            default: 'transparent',
            cssVar: '--trvlr-card-background',
            description: 'Background color of cards',
         },
         {
            key: 'cardPadding',
            label: 'Card Padding',
            type: 'range',
            default: 4,
            min: 0,
            max: 40,
            step: 2,
            unit: 'px',
            cssVar: '--trvlr-card-padding',
            description: 'Internal padding of cards',
         },
         {
            key: 'cardBorderRadius',
            label: 'Card Border Radius',
            type: 'range',
            default: 8,
            min: 0,
            max: 30,
            step: 2,
            unit: 'px',
            cssVar: '--trvlr-card-border-radius',
            description: 'Roundness of card corners',
         },
         {
            key: 'cardImageBorderRadius',
            label: 'Image Border Radius',
            type: 'range',
            default: 8,
            min: 0,
            max: 30,
            step: 2,
            unit: 'px',
            cssVar: '--trvlr-card-image-border-radius',
            description: 'Roundness of card image corners',
         },
      ],
   },
];

/**
 * Get default values for all theme settings
 */
export function getThemeDefaults(): Record<string, string | number> {
   const defaults: Record<string, string | number> = {};

   themeConfig.forEach(group => {
      group.fields.forEach(field => {
         defaults[field.key] = field.default;
      });
   });

   return defaults;
}

/**
 * Get a flat array of all fields
 */
export function getAllFields(): ThemeFieldConfig[] {
   return themeConfig.flatMap(group => group.fields);
}

/**
 * Get field config by key
 */
export function getFieldConfig(key: string): ThemeFieldConfig | undefined {
   return getAllFields().find(field => field.key === key);
}

/**
 * Generate CSS variables string from settings
 */
export function generateCSSVariables(settings: Record<string, string | number>): string {
   let css = ':root {\n';

   getAllFields().forEach(field => {
      const value = settings[field.key] ?? field.default;
      const unit = field.unit || '';
      css += `  ${field.cssVar}: ${value}${unit};\n`;
   });

   css += '}';
   return css;
}

/**
 * Merge user settings with defaults
 */
export function mergeWithDefaults(userSettings: Partial<Record<string, string | number>>): Record<string, string | number> {
   const defaults = getThemeDefaults();
   const filtered = Object.fromEntries(
      Object.entries(userSettings).filter(([_, value]) => value !== undefined)
   ) as Record<string, string | number>;

   return {
      ...defaults,
      ...filtered,
   };
}

