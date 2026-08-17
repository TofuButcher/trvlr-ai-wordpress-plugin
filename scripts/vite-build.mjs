import fs from 'node:fs';
import path from 'node:path';
import { build } from 'vite';
import react from '@vitejs/plugin-react';
import {
	cleanupEmptyJsChunks,
	discoverScssEntries,
	discoverScripts,
	emptyDir,
	ensureDir,
	publicDist,
	root,
	scriptNeedsBundle,
	scriptRoots,
	styleRoots,
	wpExternals,
	writeWpAssetFile,
} from '../vite.shared.js';

function wpAssetPlugin({ outFile, dependencies }) {
	return {
		name: 'trvlr-wp-asset',
		writeBundle(options) {
			const dir = options.dir ? path.resolve(options.dir) : root;
			writeWpAssetFile(dir, outFile, dependencies);
		},
	};
}

function cssAssetFileNames(entries) {
	return (assetInfo) => {
		const src = assetInfo.names?.[0] || assetInfo.name || '';
		const base = path.basename(src).replace(/\.(scss|sass|css)$/i, '');
		const key = Object.keys(entries).find(
			(k) => k === base || k.endsWith(`/${base}`) || path.basename(k) === base
		);
		return `${key || base}.css`;
	};
}

emptyDir(publicDist);
ensureDir(path.resolve(publicDist, 'css'));
ensureDir(path.resolve(publicDist, 'js'));
emptyDir(path.resolve(root, 'admin/css'));

for (const styleRoot of styleRoots) {
	const entries = discoverScssEntries(styleRoot);
	const keys = Object.keys(entries);
	if (!keys.length) {
		continue;
	}

	process.stdout.write(`[trvlr] building css (${path.relative(root, styleRoot.src)})…\n`);
	await build({
		configFile: false,
		root,
		base: './',
		publicDir: false,
		build: {
			manifest: false,
			emptyOutDir: false,
			outDir: styleRoot.outDir,
			sourcemap: false,
			rollupOptions: {
				input: entries,
				output: {
					assetFileNames: cssAssetFileNames(entries),
					entryFileNames: '[name].js',
				},
			},
		},
	});
	cleanupEmptyJsChunks(styleRoot.outDir, keys);
}

for (const scriptRoot of scriptRoots) {
	const scripts = discoverScripts(scriptRoot);
	ensureDir(scriptRoot.outDir);

	for (const file of scripts) {
		const relPosix = file.rel.replace(/\\/g, '/');
		const outFile = path.resolve(scriptRoot.outDir, relPosix);
		ensureDir(path.dirname(outFile));

		if (!scriptNeedsBundle(file.abs)) {
			process.stdout.write(`[trvlr] copy ${relPosix}\n`);
			fs.copyFileSync(file.abs, outFile);
			continue;
		}

		const globalName = relPosix
			.replace(/\.js$/i, '')
			.split(/[\/-]+/)
			.map((part) => part.charAt(0).toUpperCase() + part.slice(1))
			.join('');

		const baseName = path.basename(relPosix, '.js');

		process.stdout.write(`[trvlr] bundle ${relPosix}\n`);
		await build({
			configFile: false,
			root,
			base: './',
			publicDir: false,
			build: {
				emptyOutDir: false,
				outDir: publicDist,
				sourcemap: false,
				lib: {
					entry: file.abs,
					name: globalName || 'TrvlrBundle',
					formats: ['iife'],
					fileName: () => `js/${relPosix}`,
				},
				rollupOptions: {
					output: {
						assetFileNames: `css/${baseName}[extname]`,
					},
				},
			},
		});
	}
}

process.stdout.write('[trvlr] building admin react…\n');
await build({
	configFile: false,
	root,
	base: './',
	publicDir: false,
	plugins: [
		react({ jsxRuntime: 'classic' }),
		wpAssetPlugin({
			outFile: 'trvlr-admin-root.jsx.asset.php',
			dependencies: ['wp-api-fetch', 'wp-components', 'wp-element', 'wp-i18n'],
		}),
	],
	define: {
		'process.env.NODE_ENV': JSON.stringify('production'),
	},
	build: {
		emptyOutDir: true,
		outDir: path.resolve(root, 'admin/build'),
		sourcemap: false,
		lib: {
			entry: path.resolve(root, 'admin/src/trvlr-admin-root.jsx'),
			name: 'TrvlrAdmin',
			formats: ['iife'],
			fileName: () => 'trvlr-admin-root.jsx.js',
		},
		rollupOptions: {
			external: Object.keys(wpExternals),
			output: {
				globals: wpExternals,
				assetFileNames: 'trvlr-admin-root.jsx[extname]',
			},
		},
	},
});

process.stdout.write('[trvlr] build complete\n');
