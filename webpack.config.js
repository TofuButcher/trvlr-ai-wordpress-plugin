const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const fs = require('fs');

const publicStylesDir = path.join(__dirname, 'public/src/styles');
const adminStylesDir = path.join(__dirname, 'admin/styles');

const scssFiles = {};

if (fs.existsSync(publicStylesDir)) {
   fs.readdirSync(publicStylesDir)
      .filter(file => file.endsWith('.scss'))
      .forEach(file => {
         const name = path.basename(file, '.scss');
         scssFiles[`public-${name}`] = `./public/src/styles/${file}`;
      });
}

if (fs.existsSync(adminStylesDir)) {
   fs.readdirSync(adminStylesDir)
      .filter(file => file.endsWith('.scss'))
      .forEach(file => {
         const name = path.basename(file, '.scss');
         scssFiles[`admin-${name}`] = `./admin/styles/${file}`;
      });
}

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
      ...defaultConfig.plugins.filter(
         plugin => plugin.constructor.name !== 'RtlCssPlugin'
      ).map(plugin => {
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
                  let dir, name;
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
                  filesToRemove.forEach(file => {
                     if (fs.existsSync(file)) fs.unlinkSync(file);
                  });
               });
            });
         },
      },
   ],
};

