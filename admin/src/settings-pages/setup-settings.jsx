import { __ } from '@wordpress/i18n';
import {
    Card,
    CardHeader,
    CardBody,
    CardDivider,
    __experimentalHeading as Heading,
} from '@wordpress/components';
import { PageHeader } from '../components/page-header';
import { SystemStatus } from '../components/system-status';
import { ConnectionSettingsForm } from '../settings-forms/connection-settings-form';
import { EmailSettingsForm } from '../settings-forms/email-settings-form';

export const SetupSettings = () => {
    return (
        <>
            <PageHeader
                title="TRVLR.AI Setup"
                description="Configure your TRVLR AI connection settings."
            />
            <div className="trvlr-settings-section-spacer">
                <Card>
                    <CardHeader>
                        <Heading level={2}>{__('System Status', 'trvlr')}</Heading>
                    </CardHeader>
                    <CardBody>
                        <SystemStatus />
                    </CardBody>
                </Card>
                <Card>
                    <CardHeader>
                        <Heading level={2}>{__('Connection Settings', 'trvlr')}</Heading>
                    </CardHeader>
                    <CardDivider />
                    <CardBody>
                        <ConnectionSettingsForm />
                    </CardBody>
                </Card >

                <Card>
                    <CardHeader>
                        <Heading level={3}>{__('Email Notifications', 'trvlr')}</Heading>
                    </CardHeader>
                    <CardBody>
                        <EmailSettingsForm />
                    </CardBody>
                </Card>
            </div >
        </>
    );
}
