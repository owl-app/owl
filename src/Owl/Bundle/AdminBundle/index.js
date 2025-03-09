const path = require('path');
const Encore = require('@symfony/webpack-encore');

class OwlAdmin {
    static getWebpackConfig(rootDir) {
        Encore
            .setOutputPath('public/build/admin/')
            .setPublicPath('/build/admin')
            .addEntry('admin-entry', path.resolve(__dirname, 'Resources/assets/entrypoint.js'))
            .disableSingleRuntimeChunk()
            .cleanupOutputBeforeBuild()
            .enableSourceMaps(!Encore.isProduction())
            .enableVersioning(Encore.isProduction())
            .enableSassLoader((options) => {
                // eslint-disable-next-line no-param-reassign
                options.additionalData = `$rootDir: '${rootDir}';`;
            })
            .enableStimulusBridge(path.resolve(__dirname, 'Resources/assets/controllers.json'));

        const adminConfig = Encore.getWebpackConfig();

        adminConfig.externals = { ...adminConfig.externals, window: 'window', document: 'document' };
        adminConfig.name = 'admin';

        Encore.reset();

        return adminConfig;
    }
}

module.exports = OwlAdmin;
