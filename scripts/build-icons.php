<?php

/**
 * Build icon registry from icons/*.svg → icons/generated/icons.php
 *
 * CLI: php scripts/build-icons.php
 */

if (!defined('TRVLR_PLUGIN_DIR')) {
	define('TRVLR_PLUGIN_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

require_once TRVLR_PLUGIN_DIR . 'includes/class-trvlr-icons.php';

$result = Trvlr_Icons::rebuild();

if (PHP_SAPI === 'cli') {
	if (!empty($result['ok'])) {
		fwrite(STDOUT, 'Built ' . (int) $result['count'] . ' icons → ' . $result['path'] . PHP_EOL);
		exit(0);
	}
	fwrite(STDERR, 'Icon build failed: ' . ($result['error'] ?? 'unknown') . PHP_EOL);
	exit(1);
}

return $result;
