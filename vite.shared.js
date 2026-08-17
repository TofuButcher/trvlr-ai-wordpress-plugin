import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(fileURLToPath(import.meta.url));
const publicDist = path.resolve(root, 'public/dist');

export const DEFAULT_LOCAL = {
	corsOrigin: 'http://localhost',
	host: 'localhost',
	port: 5175,
};

/**
 * Nominated style roots: every non-partial .scss is compiled to outDir
 * preserving relative path (foo/bar.scss → outDir/foo/bar.css).
 */
export const styleRoots = [
	{
		src: path.resolve(root, 'public/src/styles'),
		outDir: path.resolve(root, 'public/dist/css'),
		denylist: new Set(['trvlr-theme-colors.scss']),
	},
	{
		src: path.resolve(root, 'admin/styles'),
		outDir: path.resolve(root, 'admin/css'),
		denylist: new Set(),
	},
];

/**
 * Nominated script roots: files with import/export are bundled to outDir;
 * plain scripts are copied. Relative paths preserved.
 */
export const scriptRoots = [
	{
		src: path.resolve(root, 'public/src/scripts'),
		outDir: path.resolve(root, 'public/dist/js'),
	},
];

export function loadLocalConfig() {
	const localPath = path.resolve(root, 'vite.local.json');
	if (!fs.existsSync(localPath)) {
		console.warn(
			'[trvlr] Missing vite.local.json — copy vite.local.example.json and set corsOrigin for your site.'
		);
		return { ...DEFAULT_LOCAL };
	}

	try {
		const parsed = JSON.parse(fs.readFileSync(localPath, 'utf8'));
		return {
			...DEFAULT_LOCAL,
			...parsed,
		};
	} catch (err) {
		console.warn('[trvlr] Could not parse vite.local.json:', err.message);
		return { ...DEFAULT_LOCAL };
	}
}

export function walkFiles(dir, predicate, base = dir, acc = []) {
	if (!fs.existsSync(dir)) {
		return acc;
	}

	for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
		const full = path.join(dir, ent.name);
		if (ent.isDirectory()) {
			walkFiles(full, predicate, base, acc);
			continue;
		}
		if (ent.isFile() && predicate(ent.name, full, base)) {
			const rel = path.relative(base, full).split(path.sep).join('/');
			acc.push({ abs: full, rel, name: ent.name });
		}
	}

	return acc;
}

export function discoverScssEntries(styleRoot) {
	const { src, denylist } = styleRoot;
	const files = walkFiles(src, (name) => {
		if (!name.endsWith('.scss')) {
			return false;
		}
		if (name.startsWith('_')) {
			return false;
		}
		if (denylist.has(name)) {
			return false;
		}
		return true;
	});

	const entries = {};
	for (const file of files) {
		const key = file.rel.replace(/\.scss$/i, '');
		entries[key] = file.abs;
	}
	return entries;
}

export function discoverScripts(scriptRoot) {
	return walkFiles(scriptRoot.src, (name) => name.endsWith('.js'));
}

export function scriptNeedsBundle(absPath) {
	const code = fs.readFileSync(absPath, 'utf8');
	return /^\s*import\s/m.test(code) || /^\s*export\s/m.test(code);
}

export function ensureDir(dir) {
	fs.mkdirSync(dir, { recursive: true });
}

export function emptyDir(dir) {
	if (fs.existsSync(dir)) {
		fs.rmSync(dir, { recursive: true, force: true });
	}
	fs.mkdirSync(dir, { recursive: true });
}

export function cleanupEmptyJsChunks(outDir, entryKeys) {
	for (const key of entryKeys) {
		for (const file of [
			path.resolve(outDir, `${key}.js`),
			path.resolve(outDir, `${key}.js.map`),
		]) {
			if (fs.existsSync(file)) {
				fs.unlinkSync(file);
			}
		}
	}
}

export function writeWpAssetFile(dir, outFile, dependencies) {
	const version = Date.now().toString(16);
	const deps = dependencies.map((d) => `'${d}'`).join(', ');
	const php = `<?php return array('dependencies' => array(${deps}), 'version' => '${version}');\n`;
	fs.writeFileSync(path.resolve(dir, outFile), php);
}

export const wpExternals = {
	'@wordpress/element': 'wp.element',
	'@wordpress/components': 'wp.components',
	'@wordpress/api-fetch': 'wp.apiFetch',
	'@wordpress/i18n': 'wp.i18n',
	react: 'React',
	'react-dom': 'ReactDOM',
};

export const publicSrcStyles = path.resolve(root, 'public/src/styles');
export const publicSrcScripts = path.resolve(root, 'public/src/scripts');

export { root, publicDist };
