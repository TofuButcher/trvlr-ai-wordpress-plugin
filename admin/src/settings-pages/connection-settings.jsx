import { __ } from '@wordpress/i18n';
import { PageHeading } from '../components/page-heading';
import { SystemStatus } from '../components/system-status';
import { ConnectionSettingsForm } from '../settings-forms/connection-settings-form';
import { EmailSettingsForm } from '../settings-forms/email-settings-form';

export const ConnectionSettings = () => {
    return (
        <>
            <div className="trvlr-settings-sidebar-wrap">
                <div className="trvlr-settings-section-spacer">
                    <div>
                        <PageHeading
                            text="Connect with TRVLR"
                        />
                        <ConnectionSettingsForm />
                    </div>
                    <div>
                        <PageHeading
                            text="Set up notification preference"
                        />
                        <EmailSettingsForm />
                    </div>
                </div >
                <div>
                    <PageHeading
                        text="System Status"
                    />
                    <SystemStatus />
                </div>
            </div>
        </>
    );
}
