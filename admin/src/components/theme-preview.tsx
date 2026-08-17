import React, { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import { Button, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

type PreviewCardResponse = {
	html: string;
	postId: number;
	presentationTheme: string;
};

type ThemePreviewProps = {
	presentationTheme: string;
	stylesheetUrl?: string;
};

const THEME_STYLESHEET_ID = 'trvlr-presentation-theme-css';

function ensureThemeStylesheet(url: string) {
	let link = document.getElementById(THEME_STYLESHEET_ID) as HTMLLinkElement | null;

	if (!url) {
		link?.remove();
		return;
	}

	if (!link) {
		link = document.createElement('link');
		link.id = THEME_STYLESHEET_ID;
		link.rel = 'stylesheet';
		document.head.appendChild(link);
	}

	if (link.getAttribute('href') !== url) {
		link.setAttribute('href', url);
	}
}

export const AttractionCardPreview = ({ presentationTheme, stylesheetUrl = '' }: ThemePreviewProps) => {
	const [html, setHtml] = useState('');
	const [postId, setPostId] = useState(0);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState('');
	const postIdRef = useRef(0);
	const requestIdRef = useRef(0);

	useEffect(() => {
		postIdRef.current = postId;
	}, [postId]);

	useEffect(() => {
		ensureThemeStylesheet(stylesheetUrl);
	}, [stylesheetUrl]);

	const fetchCard = useCallback(async (theme: string, options: { random?: boolean; postId?: number } = {}) => {
		if (!theme) {
			setHtml('');
			setError(__('Select a presentation theme to preview.', 'trvlr'));
			setLoading(false);
			return;
		}

		const requestId = ++requestIdRef.current;
		setLoading(true);
		setError('');

		const params = new URLSearchParams({ presentationTheme: theme });
		const useRandom = Boolean(options.random) || !options.postId;
		if (useRandom) {
			params.set('random', 'true');
			if (options.postId) {
				params.set('postId', String(options.postId));
			}
		} else if (options.postId) {
			params.set('postId', String(options.postId));
		}

		try {
			const response = await apiFetch<PreviewCardResponse>({
				path: `/trvlr/v1/settings/theme/preview-card?${params.toString()}`,
			});

			if (requestId !== requestIdRef.current) {
				return;
			}

			setHtml(response.html || '');
			setPostId(response.postId || 0);
		} catch (err: unknown) {
			if (requestId !== requestIdRef.current) {
				return;
			}

			const message =
				err && typeof err === 'object' && 'message' in err && typeof (err as { message: unknown }).message === 'string'
					? (err as { message: string }).message
					: __('Could not load attraction card preview.', 'trvlr');
			setError(message);
			setHtml('');
		} finally {
			if (requestId === requestIdRef.current) {
				setLoading(false);
			}
		}
	}, []);

	useEffect(() => {
		fetchCard(presentationTheme, {
			random: postIdRef.current === 0,
			postId: postIdRef.current || undefined,
		});
	}, [presentationTheme, fetchCard]);

	const themeClass = presentationTheme ? `trvlr--${presentationTheme}` : '';

	return (
		<div style={{ display: 'flex', flexDirection: 'column', gap: '12px', width: '100%', maxWidth: '360px' }}>
			<div
				className={themeClass}
				style={{ width: '100%' }}
				onClick={(event) => {
					const target = event.target as HTMLElement | null;
					if (target?.closest('a, button')) {
						event.preventDefault();
					}
				}}
			>
				{loading && !html ? (
					<div style={{ display: 'flex', alignItems: 'center', gap: '8px', minHeight: '120px' }}>
						<Spinner />
						<span>{__('Loading preview…', 'trvlr')}</span>
					</div>
				) : null}

				{error && !html ? (
					<p style={{ margin: 0, color: '#b32d2e' }}>{error}</p>
				) : null}

				{html ? (
					<div
						style={{ width: '100%', opacity: loading ? 0.55 : 1, transition: 'opacity 120ms ease' }}
						dangerouslySetInnerHTML={{ __html: html }}
					/>
				) : null}
			</div>

			<Button
				variant="secondary"
				style={{ width: 'fit-content', marginTop: '15px'}}
				onClick={() => fetchCard(presentationTheme, { random: true, postId: postId || undefined })}
				disabled={loading || !presentationTheme}
			>
				{__('Change Preview Attraction', 'trvlr')}
			</Button>
		</div>
	);
};
