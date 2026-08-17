import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import basicSsl from '@vitejs/plugin-basic-ssl';
import { loadLocalConfig } from './vite.shared.js';

const root = path.dirname(fileURLToPath(import.meta.url));
const hotFile = path.resolve(root, 'hot');
const local = loadLocalConfig();
const viteOrigin = `https://${local.host}:${local.port}`;

const hotFilePlugin = () => ({
	name: 'trvlr-hot-file',
	configureServer(server) {
		server.httpServer?.once('listening', () => {
			const address = server.httpServer.address();
			const port = typeof address === 'object' && address ? address.port : local.port;
			fs.writeFileSync(hotFile, `https://${local.host}:${port}`);
		});

		const clean = () => {
			if (fs.existsSync(hotFile)) {
				fs.unlinkSync(hotFile);
			}
		};
		process.on('exit', clean);
		process.on('SIGINT', () => process.exit());
		process.on('SIGTERM', () => process.exit());
	},
});

export default defineConfig({
	base: './',
	root,
	publicDir: false,
	plugins: [basicSsl(), hotFilePlugin(), react({ jsxRuntime: 'classic' })],
	resolve: {
		alias: [
			{
				find: /^@wordpress\/element$/,
				replacement: path.resolve(root, 'vite-shims/wordpress-element.js'),
			},
		],
		dedupe: ['react', 'react-dom'],
	},
	css: {
		devSourcemap: true,
	},
	server: {
		host: local.host,
		port: local.port,
		strictPort: true,
		origin: viteOrigin,
		cors: {
			origin: local.corsOrigin,
		},
		watch: {
			ignored: ['**/public/dist/**', '**/admin/build/**', '**/admin/css/**', '**/node_modules/**'],
		},
	},
	optimizeDeps: {
		include: [
			'react',
			'react-dom',
			'react-dom/client',
			'@wordpress/components',
			'@wordpress/api-fetch',
			'@wordpress/i18n',
		],
	},
});
