const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('layout', './assets/js/layout.js')
    .addEntry('admin', './assets/js/admin.js')
    .addStyleEntry('dark-error', './assets/css/dark-error.css')

    .addEntry('radio-table-show', './assets/js/radio-table/show.js')
    .addEntry('radio-table-settings', './assets/js/radio-table/settings.js')
    .addEntry('radio-table-columns', './assets/js/radio-table/columns.js')
    .addStyleEntry('radio-table-create', './assets/css/radio-table/create.css')
    .addStyleEntry('radio-table-export', './assets/css/radio-table/export.css')

    .addEntry('radio-station-edit-add', './assets/js/radio-station/edit-add.js')

    .addStyleEntry('user-login-register', './assets/css/user/login-register.css')
    .addStyleEntry('user-public-profile', './assets/css/user/public-profile.css')

    .addStyleEntry('homepage', './assets/css/general/homepage.css')
    .addStyleEntry('all-radio-tables', './assets/css/general/all-radio-tables.css')
    .addStyleEntry('static-page', './assets/css/general/static-page.css')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .copyFiles([
        { from: './assets/public', pattern: /^(?!ckeditor).*/ },
        { from: './assets/public', pattern: /ckeditor.*/, to: '[path][name].[ext]' },
    ])

    // .configureBabel((config) => {
    //     config.plugins.push('@babel/plugin-proposal-class-properties');
    // })

    // enables @babel/preset-env polyfills
    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage';
    //     config.corejs = 3;
    // })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
