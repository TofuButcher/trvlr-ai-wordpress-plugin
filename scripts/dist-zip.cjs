'use strict';

const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

const pluginRoot = path.resolve(__dirname, '..');
const zipFileName = 'trvlr-wordpress-manager.zip';
const zipPath = path.join(pluginRoot, zipFileName);
const slug = path.basename(pluginRoot);

function shouldIgnoreDir(relPosix) {
	if (relPosix === 'node_modules' || relPosix.startsWith('node_modules/')) {
		return true;
	}
	if (relPosix === '.git' || relPosix.startsWith('.git/')) {
		return true;
	}
	if (relPosix === 'admin/src' || relPosix.startsWith('admin/src/')) {
		return true;
	}
	if (relPosix === 'public/src' || relPosix.startsWith('public/src/')) {
		return true;
	}
	if (relPosix === '~api' || relPosix.startsWith('~api/')) {
		return true;
	}
	if (relPosix === '~dev' || relPosix.startsWith('~dev/')) {
		return true;
	}
	return false;
}

function shouldIgnoreFile(relPosix) {
	if (relPosix === zipFileName) {
		return true;
	}
	if (!relPosix.includes('/') && relPosix.endsWith('.zip')) {
		return true;
	}
	if (relPosix === 'package-lock.json' || relPosix === 'webpack.config.js') {
		return true;
	}
	if (relPosix === '.DS_Store') {
		return true;
	}
	return false;
}

function walk(dir, files, relBase) {
	const entries = fs.readdirSync(dir, { withFileTypes: true });
	for (const ent of entries) {
		const rel = relBase ? `${relBase}/${ent.name}` : ent.name;
		const relPosix = rel.replace(/\\/g, '/');
		if (ent.isDirectory()) {
			if (shouldIgnoreDir(relPosix)) {
				continue;
			}
			walk(path.join(dir, ent.name), files, rel);
		} else if (!shouldIgnoreFile(relPosix)) {
			files.push({ abs: path.join(dir, ent.name), rel: relPosix });
		}
	}
}

const files = [];
walk(pluginRoot, files, '');

const output = fs.createWriteStream(zipPath);
const archive = archiver('zip', { zlib: { level: 9 } });

output.on('close', () => {
	process.stdout.write(
		`Wrote ${zipFileName} (${archive.pointer()} bytes, ${files.length} files)\n`
	);
});

archive.on('error', (err) => {
	throw err;
});

archive.pipe(output);

for (const { abs, rel } of files) {
	archive.file(abs, { name: `${slug}/${rel}` });
}

archive.finalize();
