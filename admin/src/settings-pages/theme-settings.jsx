import {
    ColorPicker,
    RangeControl,
    Panel,
    PanelBody,
    Button,
    Spinner
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

function ThemeSettings() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    // Color Settings
    const [primaryColor, setPrimaryColor] = useState('hsl(245, 90%, 50%)');
    const [primaryActiveColor, setPrimaryActiveColor] = useState('hsl(245, 100%, 40%)');
    const [accentColor, setAccentColor] = useState('hsl(57, 100%, 50%)');
    const [textMutedColor, setTextMutedColor] = useState('hsl(0, 0%, 40%)');
    const [headingColor, setHeadingColor] = useState('hsl(0, 0%, 0%)');
    const [cardBackground, setCardBackground] = useState('transparent');

    // Typography
    const [headingLetterSpacing, setHeadingLetterSpacing] = useState(-0.04);

    // Attraction Card Settings
    const [attractionGridGap, setAttractionGridGap] = useState(40);
    const [attractionGridRowGap, setAttractionGridRowGap] = useState(80);
    const [cardPadding, setCardPadding] = useState(4);
    const [cardBorderRadius, setCardBorderRadius] = useState(8);
    const [cardImageBorderRadius, setCardImageBorderRadius] = useState(8);
    const [popularBadgeColor, setPopularBadgeColor] = useState('#fff');
    const [popularBadgeBackground, setPopularBadgeBackground] = useState('#000');
    const [popularBadgeFontSize, setPopularBadgeFontSize] = useState(16);

    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            const settings = await apiFetch({
                path: '/trvlr/v1/theme-settings',
            });

            console.log('Loaded theme settings:', settings);

            if (settings && typeof settings === 'object') {
                // Load colors
                if (settings.primaryColor) setPrimaryColor(settings.primaryColor);
                if (settings.primaryActiveColor) setPrimaryActiveColor(settings.primaryActiveColor);
                if (settings.accentColor) setAccentColor(settings.accentColor);
                if (settings.textMutedColor) setTextMutedColor(settings.textMutedColor);
                if (settings.headingColor) setHeadingColor(settings.headingColor);
                if (settings.cardBackground) setCardBackground(settings.cardBackground);

                // Load typography
                if (settings.headingLetterSpacing !== undefined) setHeadingLetterSpacing(settings.headingLetterSpacing);

                // Load attraction card settings
                if (settings.attractionGridGap) setAttractionGridGap(settings.attractionGridGap);
                if (settings.attractionGridRowGap) setAttractionGridRowGap(settings.attractionGridRowGap);
                if (settings.cardPadding !== undefined) setCardPadding(settings.cardPadding);
                if (settings.cardBorderRadius) setCardBorderRadius(settings.cardBorderRadius);
                if (settings.cardImageBorderRadius) setCardImageBorderRadius(settings.cardImageBorderRadius);
                if (settings.popularBadgeColor) setPopularBadgeColor(settings.popularBadgeColor);
                if (settings.popularBadgeBackground) setPopularBadgeBackground(settings.popularBadgeBackground);
                if (settings.popularBadgeFontSize) setPopularBadgeFontSize(settings.popularBadgeFontSize);
            }

            setLoading(false);
            applyCSSVariables();
        } catch (error) {
            console.error('Error loading settings:', error);
            setLoading(false);
        }
    };

    const saveSettings = async () => {
        setSaving(true);

        const settings = {
            primaryColor,
            primaryActiveColor,
            accentColor,
            textMutedColor,
            headingColor,
            cardBackground,
            headingLetterSpacing,
            attractionGridGap,
            attractionGridRowGap,
            cardPadding,
            cardBorderRadius,
            cardImageBorderRadius,
            popularBadgeColor,
            popularBadgeBackground,
            popularBadgeFontSize,
        };

        console.log('Saving settings:', settings);

        try {
            const response = await apiFetch({
                path: '/trvlr/v1/theme-settings',
                method: 'POST',
                data: settings,
            });

            console.log('Save response:', response);
            alert(__('Theme settings saved successfully!', 'trvlr'));
        } catch (error) {
            console.error('Error saving settings:', error);
            console.error('Error details:', error.message, error.data);
            alert(__('Error saving settings. Please try again. Check console for details.', 'trvlr'));
        }

        setSaving(false);
    };

    const applyCSSVariables = () => {
        const preview = document.getElementById('trvlr-preview-card');
        if (!preview) return;

        preview.style.setProperty('--trvlr-primary-color', primaryColor);
        preview.style.setProperty('--trvlr-primary-active-color', primaryActiveColor);
        preview.style.setProperty('--trvlr-accent-color', accentColor);
        preview.style.setProperty('--trvlr-text-muted-color', textMutedColor);
        preview.style.setProperty('--trvlr-heading-color', headingColor);
        preview.style.setProperty('--trvlr-heading-letter-spacing', `${headingLetterSpacing}em`);
        preview.style.setProperty('--attraction-grid-gap', `${attractionGridGap}px`);
        preview.style.setProperty('--attraction-grid-row-gap', `${attractionGridRowGap}px`);
        preview.style.setProperty('--attraction-card-background', cardBackground);
        preview.style.setProperty('--attraction-card-padding', `${cardPadding}px`);
        preview.style.setProperty('--attraction-card-border-radius', `${cardBorderRadius}px`);
        preview.style.setProperty('--attraction-card-image-border-radius', `${cardImageBorderRadius}px`);
        preview.style.setProperty('--attraction-card-popular-badge-color', popularBadgeColor);
        preview.style.setProperty('--attraction-card-popular-badge-background', popularBadgeBackground);
        preview.style.setProperty('--attraction-card-popular-badge-font-size', `${popularBadgeFontSize}px`);
    };

    // Apply CSS variables on every change (live preview)
    useEffect(() => {
        if (!loading) {
            applyCSSVariables();
        }
    }, [primaryColor, primaryActiveColor, accentColor, textMutedColor, headingColor, cardBackground,
        headingLetterSpacing, attractionGridGap, attractionGridRowGap, cardPadding, cardBorderRadius,
        cardImageBorderRadius, popularBadgeColor, popularBadgeBackground, popularBadgeFontSize, loading]);

    const resetToDefaults = () => {
        if (!confirm(__('Reset all theme settings to defaults?', 'trvlr'))) {
            return;
        }

        setPrimaryColor('hsl(245, 90%, 50%)');
        setPrimaryActiveColor('hsl(245, 100%, 40%)');
        setAccentColor('hsl(57, 100%, 50%)');
        setTextMutedColor('hsl(0, 0%, 40%)');
        setHeadingColor('hsl(0, 0%, 0%)');
        setCardBackground('transparent');
        setHeadingLetterSpacing(-0.04);
        setAttractionGridGap(40);
        setAttractionGridRowGap(80);
        setCardPadding(4);
        setCardBorderRadius(8);
        setCardImageBorderRadius(8);
        setPopularBadgeColor('#fff');
        setPopularBadgeBackground('#000');
        setPopularBadgeFontSize(16);
    };

    if (loading) {
        return (
            <div style={{ padding: '40px', textAlign: 'center' }}>
                <Spinner />
                <p>{__('Loading theme settings...', 'trvlr')}</p>
            </div>
        );
    }

    return (
        <div className="trvlr-theme-settings">
            <div style={{ display: 'flex', gap: '10px', marginBottom: '20px' }}>
                <Button
                    variant="primary"
                    onClick={saveSettings}
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

            <Panel>
                <PanelBody title={__('Colors', 'trvlr')} initialOpen={false}>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px' }}>
                        <div>
                            <strong>{__('Primary Color', 'trvlr')}</strong>
                            <ColorPicker
                                color={primaryColor}
                                onChangeComplete={(color) => setPrimaryColor(color.hex)}
                            />
                        </div>
                        <div>
                            <strong>{__('Primary Active', 'trvlr')}</strong>
                            <ColorPicker
                                color={primaryActiveColor}
                                onChangeComplete={(color) => setPrimaryActiveColor(color.hex)}
                            />
                        </div>
                        <div>
                            <strong>{__('Accent Color', 'trvlr')}</strong>
                            <ColorPicker
                                color={accentColor}
                                onChangeComplete={(color) => setAccentColor(color.hex)}
                            />
                        </div>
                        <div>
                            <strong>{__('Text Muted', 'trvlr')}</strong>
                            <ColorPicker
                                color={textMutedColor}
                                onChangeComplete={(color) => setTextMutedColor(color.hex)}
                            />
                        </div>
                        <div>
                            <strong>{__('Heading Color', 'trvlr')}</strong>
                            <ColorPicker
                                color={headingColor}
                                onChangeComplete={(color) => setHeadingColor(color.hex)}
                            />
                        </div>
                        <div>
                            <strong>{__('Card Background', 'trvlr')}</strong>
                            <ColorPicker
                                color={cardBackground}
                                onChangeComplete={(color) => setCardBackground(color.hex)}
                            />
                        </div>
                    </div>
                </PanelBody>

                <PanelBody title={__('Typography', 'trvlr')} initialOpen={false}>
                    <RangeControl
                        label={__('Heading Letter Spacing', 'trvlr')}
                        value={headingLetterSpacing}
                        onChange={setHeadingLetterSpacing}
                        min={-0.1}
                        max={0.1}
                        step={0.01}
                    />
                </PanelBody>

                <PanelBody title={__('Attraction Cards', 'trvlr')} initialOpen={false}>
                    <RangeControl
                        label={__('Grid Gap', 'trvlr')}
                        value={attractionGridGap}
                        onChange={setAttractionGridGap}
                        min={10}
                        max={80}
                        step={5}
                    />
                    <RangeControl
                        label={__('Grid Row Gap', 'trvlr')}
                        value={attractionGridRowGap}
                        onChange={setAttractionGridRowGap}
                        min={20}
                        max={120}
                        step={10}
                    />
                    <RangeControl
                        label={__('Card Padding', 'trvlr')}
                        value={cardPadding}
                        onChange={setCardPadding}
                        min={0}
                        max={20}
                        step={1}
                    />
                    <RangeControl
                        label={__('Card Border Radius', 'trvlr')}
                        value={cardBorderRadius}
                        onChange={setCardBorderRadius}
                        min={0}
                        max={30}
                        step={1}
                    />
                    <RangeControl
                        label={__('Card Image Border Radius', 'trvlr')}
                        value={cardImageBorderRadius}
                        onChange={setCardImageBorderRadius}
                        min={0}
                        max={30}
                        step={1}
                    />
                    <div style={{ marginTop: '20px' }}>
                        <strong>{__('Popular Badge', 'trvlr')}</strong>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginTop: '10px' }}>
                            <div>
                                <label style={{ fontSize: '13px', display: 'block', marginBottom: '5px' }}>{__('Text Color', 'trvlr')}</label>
                                <ColorPicker
                                    color={popularBadgeColor}
                                    onChangeComplete={(color) => setPopularBadgeColor(color.hex)}
                                />
                            </div>
                            <div>
                                <label style={{ fontSize: '13px', display: 'block', marginBottom: '5px' }}>{__('Background', 'trvlr')}</label>
                                <ColorPicker
                                    color={popularBadgeBackground}
                                    onChangeComplete={(color) => setPopularBadgeBackground(color.hex)}
                                />
                            </div>
                        </div>
                    </div>
                    <RangeControl
                        label={__('Popular Badge Font Size', 'trvlr')}
                        value={popularBadgeFontSize}
                        onChange={setPopularBadgeFontSize}
                        min={12}
                        max={24}
                        step={1}
                    />
                </PanelBody>
            </Panel>

            <div style={{ marginTop: '20px' }}>
                <Button
                    variant="primary"
                    onClick={saveSettings}
                    isBusy={saving}
                    disabled={saving}
                >
                    {saving ? __('Saving...', 'trvlr') : __('Save Settings', 'trvlr')}
                </Button>
            </div>
        </div>
    );
}

// Render the component
import { render } from '@wordpress/element';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('trvlr-theme-settings-root');
    if (root) {
        render(<ThemeSettings />, root);
    }
});
