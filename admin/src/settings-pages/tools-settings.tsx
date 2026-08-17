import React, { useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardBody,
	Notice,
	ToggleControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { PageHeading } from '../components/page-heading';
import { useTrvlr } from '../context/TrvlrContext';

type InvalidField = {
	key: string;
	reason: string;
};

type ImportResponse = {
	success: boolean;
	needsConfirmation?: boolean;
	invalid?: InvalidField[];
	valid?: Record<string, unknown>;
	settings?: Record<string, unknown>;
	skipped?: InvalidField[];
	importedKeys?: string[];
	message?: string;
};

export const ToolsSettings = () => {
	const {
		connectionSettings,
		saveConnectionSettings,
		saving,
		applyThemeSettingsLocally,
	} = useTrvlr();
	const fileInputRef = useRef<HTMLInputElement | null>(null);

	const [exporting, setExporting] = useState(false);
	const [importing, setImporting] = useState(false);
	const [pendingPayload, setPendingPayload] = useState<unknown>(null);
	const [invalidFields, setInvalidFields] = useState<InvalidField[]>([]);
	const [message, setMessage] = useState<{
		type: 'success' | 'error' | 'warning' | 'info';
		text: string;
	} | null>(null);
	const [featuresSaveStatus, setFeaturesSaveStatus] = useState<
		'success' | 'error' | null
	>(null);

	const [disablePostType, setDisablePostType] = useState(
		!!connectionSettings.disable_attraction_post_type
	);
	const [disableSync, setDisableSync] = useState(
		!!connectionSettings.disable_attraction_sync
	);
	const [disableFrontendBooking, setDisableFrontendBooking] = useState(
		!!connectionSettings.disable_frontend_booking
	);
	const [disableSeoSchema, setDisableSeoSchema] = useState(
		!!connectionSettings.disable_attraction_seo_schema
	);

	useEffect(() => {
		setDisablePostType(!!connectionSettings.disable_attraction_post_type);
		setDisableSync(!!connectionSettings.disable_attraction_sync);
		setDisableFrontendBooking(!!connectionSettings.disable_frontend_booking);
		setDisableSeoSchema(!!connectionSettings.disable_attraction_seo_schema);
	}, [connectionSettings]);

	const onDisablePostTypeChange = (value: boolean) => {
		setDisablePostType(value);
		if (value) {
			setDisableSync(true);
		}
	};

	const handleSaveFeatures = async () => {
		setFeaturesSaveStatus(null);

		const result = await saveConnectionSettings({
			disable_attraction_post_type: disablePostType,
			disable_attraction_sync: disablePostType ? true : disableSync,
			disable_frontend_booking: disableFrontendBooking,
			disable_attraction_seo_schema: disableSeoSchema,
		});

		if (result.success) {
			setFeaturesSaveStatus('success');
			setTimeout(() => setFeaturesSaveStatus(null), 3000);
		} else {
			console.error('Error saving feature settings:', result.error);
			setFeaturesSaveStatus('error');
		}
	};

	const syncToggleChecked = disablePostType ? true : disableSync;
	const syncToggleDisabled = disablePostType;

	const downloadExport = async () => {
		setExporting(true);
		setMessage(null);
		try {
			const payload = await apiFetch<Record<string, unknown>>({
				path: '/trvlr/v1/settings/theme/export',
			});
			const blob = new Blob([JSON.stringify(payload, null, 2)], {
				type: 'application/json',
			});
			const url = URL.createObjectURL(blob);
			const anchor = document.createElement('a');
			const stamp = new Date().toISOString().slice(0, 10);
			anchor.href = url;
			anchor.download = `trvlr-theme-settings-${stamp}.json`;
			document.body.appendChild(anchor);
			anchor.click();
			anchor.remove();
			URL.revokeObjectURL(url);
			setMessage({
				type: 'success',
				text: __('Theme settings exported.', 'trvlr'),
			});
		} catch (error) {
			console.error(error);
			setMessage({
				type: 'error',
				text: __('Failed to export theme settings.', 'trvlr'),
			});
		} finally {
			setExporting(false);
		}
	};

	const runImport = async (payload: unknown, skipInvalid: boolean) => {
		setImporting(true);
		setMessage(null);
		try {
			const response = await apiFetch<ImportResponse>({
				path: '/trvlr/v1/settings/theme/import',
				method: 'POST',
				data: {
					payload,
					skipInvalid,
				},
			});

			if (response?.needsConfirmation && !skipInvalid) {
				setPendingPayload(payload);
				setInvalidFields(response.invalid || []);
				setMessage({
					type: 'warning',
					text:
						response.message ||
						__('Some settings in the import file are invalid or unknown.', 'trvlr'),
				});
				return;
			}

			if (response?.success && response.settings) {
				applyThemeSettingsLocally(response.settings);
				setPendingPayload(null);
				setInvalidFields([]);
				const skippedCount = response.skipped?.length || 0;
				setMessage({
					type: 'success',
					text:
						skippedCount > 0
							? __(
									'Theme settings imported. Invalid or unknown fields were skipped. Other settings were reset to defaults.',
									'trvlr'
								)
							: __(
									'Theme settings imported. Settings not in the file were reset to defaults.',
									'trvlr'
								),
				});
				if (fileInputRef.current) {
					fileInputRef.current.value = '';
				}
				return;
			}

			setMessage({
				type: 'error',
				text: __('Import failed.', 'trvlr'),
			});
		} catch (error: any) {
			console.error(error);
			setMessage({
				type: 'error',
				text:
					error?.message ||
					__('Failed to import theme settings.', 'trvlr'),
			});
		} finally {
			setImporting(false);
		}
	};

	const onFileSelected = async (event: { target?: HTMLInputElement | null }) => {
		const file = event.target?.files?.[0];
		if (!file) {
			return;
		}

		setPendingPayload(null);
		setInvalidFields([]);
		setMessage(null);

		try {
			const text = await file.text();
			const payload = JSON.parse(text);
			await runImport(payload, false);
		} catch (error) {
			console.error(error);
			setMessage({
				type: 'error',
				text: __('Could not read that file as JSON.', 'trvlr'),
			});
			if (fileInputRef.current) {
				fileInputRef.current.value = '';
			}
		}
	};

	return (
		<div>
			<PageHeading text={__('Tools', 'trvlr')} />

			<section style={{ marginBottom: '2rem' }}>
				<h2 className="trvlr-settings-heading" style={{ marginBottom: '0.25rem' }}>
					{__('Traveloris features', 'trvlr')}
				</h2>
				<p style={{ marginTop: 0, marginBottom: '1rem', color: '#646970', fontSize: '13px' }}>
					{__('Turn off what you don’t need', 'trvlr')}
				</p>

				{featuresSaveStatus === 'success' && (
					<Notice status="success" isDismissible={false}>
						{__('Settings saved successfully!', 'trvlr')}
					</Notice>
				)}
				{featuresSaveStatus === 'error' && (
					<Notice status="error" isDismissible={false}>
						{__('Error saving settings. Please try again.', 'trvlr')}
					</Notice>
				)}

				<ToggleControl
					label={__('Disable Traveloris Attraction post type', 'trvlr')}
					help={__(
						'When enabled, the plugin does not register the trvlr_attraction post type. Use your own post types or content; syncing from Traveloris is turned off while this is enabled.',
						'trvlr'
					)}
					checked={disablePostType}
					onChange={onDisablePostTypeChange}
				/>

				<ToggleControl
					label={__('Disable syncing attractions from Traveloris', 'trvlr')}
					help={__(
						'When enabled, scheduled and manual catalog sync do not run. Per-attraction sync controls are unavailable.',
						'trvlr'
					)}
					checked={syncToggleChecked}
					disabled={syncToggleDisabled}
					onChange={(v) => !syncToggleDisabled && setDisableSync(v)}
				/>
				{syncToggleDisabled && (
					<p
						style={{
							margin: '-8px 0 12px',
							paddingLeft: '48px',
							fontSize: '12px',
							color: '#646970',
						}}
					>
						{__(
							'Unavailable while the Traveloris Attraction post type is disabled.',
							'trvlr'
						)}
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

				<ToggleControl
					label={__('Disable SEO schema injection', 'trvlr')}
					help={__(
						'When enabled, TouristAttraction and FAQPage JSON-LD from Traveloris seo_metadata is not output on attraction pages. Use this if you manage schema entirely with an SEO plugin like Rank Math or Yoast.',
						'trvlr'
					)}
					checked={disableSeoSchema}
					onChange={setDisableSeoSchema}
				/>

				<Button
					variant="primary"
					onClick={handleSaveFeatures}
					isBusy={saving}
					disabled={saving || exporting || importing}
					style={{ marginTop: '0.5rem' }}
				>
					{saving ? __('Saving...', 'trvlr') : __('Save feature settings', 'trvlr')}
				</Button>
			</section>

			<section>
				<h2 className="trvlr-settings-heading" style={{ marginBottom: '0.25rem' }}>
					{__('Theme settings transfer', 'trvlr')}
				</h2>
				<p style={{ marginTop: 0, maxWidth: '62ch', color: '#50575e' }}>
					{__(
						'Export or import theme appearance settings (custom values and presentation theme). Import replaces current theme settings: keys missing from the file reset to defaults.',
						'trvlr'
					)}
				</p>

				{message && (
					<Notice
						status={message.type}
						isDismissible
						onRemove={() => setMessage(null)}
						style={{ marginBottom: '16px' }}
					>
						{message.text}
					</Notice>
				)}

				<div
					style={{
						display: 'grid',
						gap: '16px',
						gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
					}}
				>
					<Card>
						<CardBody>
							<h3 style={{ marginTop: 0 }}>{__('Export theme settings', 'trvlr')}</h3>
							<p style={{ color: '#50575e', fontSize: '13px' }}>
								{__(
									'Downloads a JSON file of custom theme values stored for this site, including the presentation theme variant.',
									'trvlr'
								)}
							</p>
							<Button
								variant="primary"
								onClick={downloadExport}
								isBusy={exporting}
								disabled={exporting || importing}
							>
								{exporting
									? __('Exporting…', 'trvlr')
									: __('Export theme settings', 'trvlr')}
							</Button>
						</CardBody>
					</Card>

					<Card>
						<CardBody>
							<h3 style={{ marginTop: 0 }}>{__('Import theme settings', 'trvlr')}</h3>
							<p style={{ color: '#50575e', fontSize: '13px' }}>
								{__(
									'Upload a Traveloris theme settings JSON export. Matching fields are applied; everything else returns to defaults.',
									'trvlr'
								)}
							</p>
							<input
								ref={fileInputRef}
								type="file"
								accept="application/json,.json"
								disabled={importing}
								onChange={onFileSelected}
							/>
						</CardBody>
					</Card>
				</div>

				{invalidFields.length > 0 && (
					<Card style={{ marginTop: '16px' }}>
						<CardBody>
							<h3 style={{ marginTop: 0 }}>
								{__('Invalid or unknown settings', 'trvlr')}
							</h3>
							<p style={{ color: '#50575e', fontSize: '13px' }}>
								{__(
									'These fields will not be imported. You can skip them and continue with the valid settings only.',
									'trvlr'
								)}
							</p>
							<ul style={{ margin: '0 0 16px', paddingLeft: '1.2em' }}>
								{invalidFields.map((field) => (
									<li key={field.key} style={{ marginBottom: '6px' }}>
										<code>{field.key}</code>
										{' — '}
										{field.reason}
									</li>
								))}
							</ul>
							<div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
								<Button
									variant="primary"
									isBusy={importing}
									disabled={importing || pendingPayload == null}
									onClick={() => runImport(pendingPayload, true)}
								>
									{__(
										'Skip those settings and continue with import',
										'trvlr'
									)}
								</Button>
								<Button
									variant="secondary"
									disabled={importing}
									onClick={() => {
										setPendingPayload(null);
										setInvalidFields([]);
										setMessage(null);
										if (fileInputRef.current) {
											fileInputRef.current.value = '';
										}
									}}
								>
									{__('Cancel', 'trvlr')}
								</Button>
							</div>
						</CardBody>
					</Card>
				)}
			</section>
		</div>
	);
};
