(function ($) {
	'use strict';

	var config = window.trvlrFieldSyncUI || {};
	var customFields = Array.isArray(config.customFields) ? config.customFields.slice() : [];

	function isCustom(field) {
		return customFields.indexOf(field) !== -1;
	}

	function setEditorReadonly(editorId, readonly) {
		var $ta = $('#' + editorId);
		if ($ta.length) {
			$ta.prop('readonly', readonly);
		}
		if (typeof tinymce !== 'undefined') {
			var ed = tinymce.get(editorId);
			if (ed) {
				ed.setMode(readonly ? 'readonly' : 'design');
			}
		}
	}

	function applyFieldState($wrap) {
		var field = $wrap.data('field');
		var custom = isCustom(field);
		var $body = $wrap.children('.trvlr-field-sync-body');
		var $controls = $body.find('input, textarea, select, button');

		$wrap.toggleClass('is-custom-edit', custom);
		$wrap.toggleClass('is-synced', !custom);
		$wrap.find('.trvlr-field-mode-badge').text(
			custom ? config.i18n.badgeCustom : config.i18n.badgeSynced
		);
		$wrap.find('.trvlr-field-mode-toggle').text(
			custom ? config.i18n.enableSync : config.i18n.customEdit
		);

		$controls.each(function () {
			var $el = $(this);
			if ($el.is('input[type="checkbox"], input[type="radio"], select, button')) {
				$el.prop('disabled', !custom);
			} else {
				$el.prop('readonly', !custom);
				$el.prop('disabled', !custom);
			}
		});

		$body.find('.wp-editor-area').each(function () {
			setEditorReadonly(this.id, !custom);
		});

		if (field === 'trvlr_media') {
			$body.find('.trvlr-media-gallery .remove, button').prop('disabled', !custom);
		}

		if (field === '_thumbnail_id') {
			$body.find('#set-post-thumbnail, #remove-post-thumbnail').css(
				'pointer-events',
				custom ? '' : 'none'
			);
		}

		if (field === 'post_title') {
			$body.find('#title').prop('readonly', !custom).prop('disabled', !custom);
		}
	}

	function applyAll() {
		$('.trvlr-field-sync').each(function () {
			applyFieldState($(this));
		});
	}

	function ensureChrome(selector, field, label) {
		var $target = $(selector);
		if (!$target.length || $target.closest('.trvlr-field-sync').length) {
			return;
		}

		var custom = isCustom(field);
		var $wrap = $('<div class="trvlr-field-sync" />')
			.attr('data-field', field)
			.toggleClass('is-custom-edit', custom)
			.toggleClass('is-synced', !custom);

		var $bar = $(
			'<div class="trvlr-field-sync-bar">' +
				(label ? '<span class="trvlr-field-sync-label"></span>' : '') +
				'<span class="trvlr-field-mode-badge"></span>' +
				'<button type="button" class="button-link trvlr-field-mode-toggle"></button>' +
				'</div>'
		);
		if (label) {
			$bar.find('.trvlr-field-sync-label').text(label);
		}

		var $body = $('<div class="trvlr-field-sync-body" />');
		$target.before($wrap);
		$wrap.append($bar).append($body);
		$body.append($target);
		applyFieldState($wrap);
	}

	function toggleField(field, enableCustom) {
		return $.ajax({
			url: config.ajaxUrl,
			type: 'POST',
			data: {
				action: 'trvlr_set_field_edit_mode',
				nonce: config.nonce,
				post_id: config.postId,
				field: field,
				enabled: enableCustom ? '1' : '0'
			}
		}).done(function (response) {
			if (!response || !response.success) {
				window.alert((response && response.data && response.data.message) || config.i18n.error);
				return;
			}
			customFields = Array.isArray(response.data.custom_fields)
				? response.data.custom_fields
				: customFields;
			applyAll();
			if (window.trvlrUpdateCustomEditsSidebar) {
				window.trvlrUpdateCustomEditsSidebar(customFields, response.data.labels || {});
			}
		});
	}

	$(function () {
		if (!config.postId) {
			return;
		}

		ensureChrome('#titlewrap', 'post_title', config.i18n.titleLabel || 'Title');
		ensureChrome('#postimagediv .inside', '_thumbnail_id', config.i18n.featuredLabel || 'Featured Image');

		$(document).on('tinymce-editor-init', function (event, editor) {
			var $wrap = $('#' + editor.id).closest('.trvlr-field-sync');
			if ($wrap.length) {
				applyFieldState($wrap);
			}
		});

		applyAll();

		$(document).on('click', '.trvlr-field-mode-toggle', function (e) {
			e.preventDefault();
			var $wrap = $(this).closest('.trvlr-field-sync');
			var field = $wrap.data('field');
			var enableCustom = !isCustom(field);

			if (!enableCustom && !window.confirm(config.i18n.confirmEnableSync)) {
				return;
			}

			var $btn = $(this).prop('disabled', true);
			toggleField(field, enableCustom).always(function () {
				$btn.prop('disabled', false);
			});
		});
	});
})(jQuery);
