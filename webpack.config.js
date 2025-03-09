const path = require('path');
const Encore = require('@symfony/webpack-encore');

const syliusBundles = path.resolve(__dirname, 'src/Owl/Bundle/');
//path.resolve(__dirname, 'vendor/sylius/sylius/src/Sylius/Bundle/')
const uiBundleScripts = path.resolve(__dirname, 'src/Owl/Bundle/UiBundle/Resources/private/js/');
const uiBundleResources = path.resolve(__dirname, 'src/Owl/Bundle/UiBundle/Resources/private/');

// Admin config
Encore
  .setOutputPath('public/build/admin/')
  .setPublicPath('/build/admin')
  .addEntry('admin-entry', './assets/admin/entry.js')
  .disableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(Encore.isProduction())
  .enableVersioning(false)
  .enableStimulusBridge('./assets/admin/controllers.json')
  .enableSassLoader()
  .configureImageRule({
    filename: 'images/[name][ext]'
  })
  .configureFontRule({
    filename: 'fonts/[name][ext]'
  });

const adminConfig = Encore.getWebpackConfig();

adminConfig.resolve.alias['owl/ui'] = uiBundleScripts;
adminConfig.resolve.alias['owl/ui-resources'] = uiBundleResources;
adminConfig.resolve.alias['Owl/Bundle'] = syliusBundles;
adminConfig.externals = Object.assign({}, adminConfig.externals, { window: 'window', document: 'document' });
adminConfig.name = 'admin';

module.exports = [adminConfig];
