import { useState, useEffect } from '@wordpress/element';
import { render } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
    Panel,
    PanelBody,
    PanelRow,
    TextControl,
    Button,
    Spinner,
    Notice
} from '@wordpress/components';

function SetupSettings() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [saveStatus, setSaveStatus] = useState(null);

    const [organisationId, setOrganisationId] = useState('');
    const [apiKey, setApiKey] = useState('');
    const [notificationEmail, setNotificationEmail] = useState('');
    const [notifyErrors, setNotifyErrors] = useState(true);
    const [notifyComplete, setNotifyComplete] = useState(false);
    const [notifyWeekly, setNotifyWeekly] = useState(false);

    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            const response = await apiFetch({
                path: '/wp/v2/settings',
            });

            if (response.trvlr_organisation_id) {
                setOrganisationId(response.trvlr_organisation_id);
            }
            if (response.trvlr_api_key) {
                setApiKey(response.trvlr_api_key);
            }

            const notificationSettings = response.trvlr_notification_settings || {};
            if (notificationSettings.email) {
                setNotificationEmail(notificationSettings.email);
            }
            if (typeof notificationSettings.notify_errors !== 'undefined') {
                setNotifyErrors(notificationSettings.notify_errors);
            }
            if (typeof notificationSettings.notify_complete !== 'undefined') {
                setNotifyComplete(notificationSettings.notify_complete);
            }
            if (typeof notificationSettings.notify_weekly !== 'undefined') {
                setNotifyWeekly(notificationSettings.notify_weekly);
            }

            setLoading(false);
        } catch (error) {
            console.error('Error loading settings:', error);
            setLoading(false);
        }
    };

    const saveSettings = async () => {
        setSaving(true);
        setSaveStatus(null);

        try {
            await apiFetch({
                path: '/wp/v2/settings',
                method: 'POST',
                data: {
                    trvlr_organisation_id: organisationId,
                    trvlr_api_key: apiKey,
                    trvlr_notification_settings: {
                        email: notificationEmail,
                        notify_errors: notifyErrors,
                        notify_complete: notifyComplete,
                        notify_weekly: notifyWeekly,
                    },
                },
            });

            setSaveStatus('success');
            setTimeout(() => setSaveStatus(null), 3000);
        } catch (error) {
            console.error('Error saving settings:', error);
            setSaveStatus('error');
        }

        setSaving(false);
    };

    if (loading) {
        return (
            <div style={{ padding: '40px', textAlign: 'center' }}>
                <Spinner />
                <p>{__('Loading setup settings...', 'trvlr')}</p>
            </div>
        );
    }

    return (
        <div className="trvlr-setup-settings">
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

            <Panel>
                <PanelBody title={__('Connection Settings', 'trvlr')} initialOpen={true}>
                    <PanelRow>
                        <div style={{ width: '100%' }}>
                            <TextControl
                                label={__('Organisation ID', 'trvlr')}
                                value={organisationId}
                                onChange={setOrganisationId}
                                help={__('Your Organisation ID from TRVLR AI.', 'trvlr')}
                            />
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <div style={{ width: '100%' }}>
                            <TextControl
                                label={__('API Key', 'trvlr')}
                                value={apiKey}
                                onChange={setApiKey}
                                help={__('API Key for authentication (if required).', 'trvlr')}
                                type="password"
                            />
                        </div>
                    </PanelRow>
                </PanelBody>

                <PanelBody title={__('Email Notifications', 'trvlr')}>
                    <PanelRow>
                        <div style={{ width: '100%' }}>
                            <TextControl
                                label={__('Notification Email', 'trvlr')}
                                value={notificationEmail}
                                onChange={setNotificationEmail}
                                help={__('Email address for sync notifications.', 'trvlr')}
                                type="email"
                            />
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>
                            <input
                                type="checkbox"
                                checked={notifyErrors}
                                onChange={(e) => setNotifyErrors(e.target.checked)}
                            />
                            {' '}{__('Notify on sync errors', 'trvlr')}
                        </label>
                    </PanelRow>
                    <PanelRow>
                        <label>
                            <input
                                type="checkbox"
                                checked={notifyComplete}
                                onChange={(e) => setNotifyComplete(e.target.checked)}
                            />
                            {' '}{__('Notify on sync completion', 'trvlr')}
                        </label>
                    </PanelRow>
                    <PanelRow>
                        <label>
                            <input
                                type="checkbox"
                                checked={notifyWeekly}
                                onChange={(e) => setNotifyWeekly(e.target.checked)}
                            />
                            {' '}{__('Send weekly summary', 'trvlr')}
                        </label>
                    </PanelRow>
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

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('trvlr-setup-settings-root');
    if (root) {
        render(<SetupSettings />, root);
    }
});

