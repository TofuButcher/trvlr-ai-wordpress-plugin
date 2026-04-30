const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const fs = require('fs');

const publicStylesDir = path.join(__dirname, 'public/src/styles');
const adminStylesDir = path.join(__dirname, 'admin/styles');

/**
 * Recursively collect .scss files under rootDir.
 * @param {string} rootDir Absolute path to styles root
 * @param {string} entryPrefix Webpack entry key prefix (e.g. "public" or "admin")
 * @returns {Record<string, string>} Map of entryName -> import path starting with ./
 */
function collectScssEntriesRecursive(rootDir, entryPrefix) {
	const entries = {};
	if (!fs.existsSync(rootDir)) {
		return entries;
	}

	const walk = (currentDir) => {
		for (const ent of fs.readdirSync(currentDir, { withFileTypes: true })) {
			const fullPath = path.join(currentDir, ent.name);
			if (ent.isDirectory()) {
				walk(fullPath);
			} else if (ent.isFile() && ent.name.endsWith('.scss')) {
				const relFromRoot = path.relative(rootDir, fullPath);
				const slug = relFromRoot
					.replace(/\\/g, '/')
					.replace(/\//g, '-')
					.replace(/\.scss$/i, '');
				const importPath =
					'./' + path.relative(__dirname, fullPath).split(path.sep).join('/');
				entries[`${entryPrefix}-${slug}`] = importPath;
			}
		}
	};

	walk(rootDir);
	return entries;
}

const scssFiles = {
	...collectScssEntriesRecursive(publicStylesDir, 'public'),
	...collectScssEntriesRecursive(adminStylesDir, 'admin'),
};

module.exports = {
	...defaultConfig,
	devtool: process.env.NODE_ENV === 'production' ? false : 'source-map',
	entry: {
		'admin/build/trvlr-admin-root.jsx': './admin/src/trvlr-admin-root.jsx',
		...scssFiles,
	},
	output: {
		path: path.resolve(__dirname),
		filename: (pathData) => {
			const chunkName = pathData.chunk.name;
			if (chunkName.startsWith('public-')) {
				return 'public/css/[name].js';
			}
			if (chunkName.startsWith('admin-')) {
				return 'admin/css/[name].js';
			}
			return '[name].js';
		},
	},
	plugins: [
		...defaultConfig.plugins
			.filter((plugin) => plugin.constructor.name !== 'RtlCssPlugin')
			.map((plugin) => {
				if (plugin.constructor.name === 'MiniCssExtractPlugin') {
					const MiniCssExtractPlugin = require('mini-css-extract-plugin');
					return new MiniCssExtractPlugin({
						filename: (pathData) => {
							const chunkName = pathData.chunk.name;
							if (chunkName.startsWith('public-')) {
								const name = chunkName.replace('public-', '');
								return `public/css/${name}.css`;
							}
							if (chunkName.startsWith('admin-')) {
								const name = chunkName.replace('admin-', '');
								return `admin/css/${name}.css`;
							}
							return '[name].css';
						},
					});
				}
				return plugin;
			}),
		{
			apply: (compiler) => {
				compiler.hooks.done.tap('CleanupScssArtifacts', () => {
					Object.keys(scssFiles).forEach((chunkName) => {
						let dir;
						let name;
						if (chunkName.startsWith('public-')) {
							dir = 'public/css';
							name = chunkName.replace('public-', '');
						} else if (chunkName.startsWith('admin-')) {
							dir = 'admin/css';
							name = chunkName.replace('admin-', '');
						} else {
							return;
						}

						const filesToRemove = [
							path.join(__dirname, dir, `${chunkName}.js`),
							path.join(__dirname, dir, `${chunkName}.js.map`),
							path.join(__dirname, dir, `${chunkName}.asset.php`),
							path.join(__dirname, dir, `${name}.js`),
							path.join(__dirname, dir, `${name}.js.map`),
							path.join(__dirname, dir, `${name}.asset.php`),
							path.join(__dirname, dir, `${name}-rtl.css`),
							path.join(__dirname, dir, `${name}.css.map`),
						];
						filesToRemove.forEach((file) => {
							if (fs.existsSync(file)) fs.unlinkSync(file);
						});
					});
				});
			},
		},
	],
};
