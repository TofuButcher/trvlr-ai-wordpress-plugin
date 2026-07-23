'use strict';

const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

const pluginRoot = path.resolve(__dirname, '..');
const slug = path.basename(pluginRoot);

const builds = [
	{
		zipFileName: 'trvlr-wordpress-manager.zip',
		includeDev: false,
	},
	{
		zipFileName: 'dev-trvlr-wordpress-manager.zip',
		includeDev: true,
	},
];

function shouldIgnoreDir(relPosix, includeDev) {
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
	if (relPosix === '~skills' || relPosix.startsWith('~skills/')) {
		return true;
	}
	if (!includeDev && (relPosix === '~dev' || relPosix.startsWith('~dev/'))) {
		return true;
	}
	return false;
}

function shouldIgnoreFile(relPosix) {
	if (builds.some((b) => b.zipFileName === relPosix)) {
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

function walk(dir, files, relBase, includeDev) {
	const entries = fs.readdirSync(dir, { withFileTypes: true });
	for (const ent of entries) {
		const rel = relBase ? `${relBase}/${ent.name}` : ent.name;
		const relPosix = rel.replace(/\\/g, '/');
		if (ent.isDirectory()) {
			if (shouldIgnoreDir(relPosix, includeDev)) {
				continue;
			}
			walk(path.join(dir, ent.name), files, rel, includeDev);
		} else if (!shouldIgnoreFile(relPosix)) {
			files.push({ abs: path.join(dir, ent.name), rel: relPosix });
		}
	}
}

function createZip({ zipFileName, includeDev }) {
	return new Promise((resolve, reject) => {
		const zipPath = path.join(pluginRoot, zipFileName);
		const files = [];
		walk(pluginRoot, files, '', includeDev);

		const output = fs.createWriteStream(zipPath);
		const archive = archiver('zip', { zlib: { level: 9 } });

		output.on('close', () => {
			process.stdout.write(
				`Wrote ${zipFileName} (${archive.pointer()} bytes, ${files.length} files)\n`
			);
			resolve();
		});

		archive.on('error', reject);
		output.on('error', reject);

		archive.pipe(output);

		for (const { abs, rel } of files) {
			archive.file(abs, { name: `${slug}/${rel}` });
		}

		archive.finalize();
	});
}

(async () => {
	for (const build of builds) {
		await createZip(build);
	}
})().catch((err) => {
	console.error(err);
	process.exit(1);
});
