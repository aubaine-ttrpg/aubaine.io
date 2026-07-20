import Encore from '@symfony/webpack-encore';

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
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    // The print document is a separate, isolated bundle (no editor chrome).
    .addEntry('print', './assets/print.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // enables the Symfony UX Stimulus bridge (used in assets/stimulus_bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

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

    // Displays build status system notifications to the user
    // .enableBuildNotifications()

    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // PostCSS + autoprefixer, targets driven by .browserslistrc (single source of truth)
    .enablePostCssLoader()

    // Cover art, node icons, and paper textures, fingerprinted and referenced
    // via the manifest (see catalyst_image() in AppExtension).
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpe?g|svg|webp)$/,
    })

    // Brand logos live once in @aubaine/sigil; emit them so the favicon and any
    // <img> can reference them by URL (the topbar mark is drawn via logo.css).
    .copyFiles({
        from: '../sigil/src/brand/logo',
        to: 'images/brand/[name].[hash:8].[ext]',
        pattern: /\.svg$/,
    })

    // Configure JS and CSS minimizers
    // .configureJsMinimizerPlugin((options, MinimizerPlugin) => {
    //     options.minify = MinimizerPlugin.esbuildMinify
    // })
    // .configureCssMinimizerPlugin((options, MinimizerPlugin) => {
    //     options.minify = MinimizerPlugin.lightningCssMinify;
    // })

    // configure Babel
    .configureBabel((config) => {
        config.plugins.push(['polyfill-corejs3', { method: 'usage-global', version: '3.49' }]);
    }, {
        // @aubaine/sigil is a file: dependency: its realpath sits outside
        // node_modules, so Babel would otherwise transpile it and inject core-js
        // imports it can't resolve. It ships modern ESM for the same browsers, so
        // skip it like any other dependency (this replaces the default exclude).
        exclude: /node_modules|[/\\]sigil[/\\]/,
    })

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

export default await Encore.getWebpackConfig();
