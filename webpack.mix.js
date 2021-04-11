const mix = require('laravel-mix');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vue();

mix.webpackConfig({
    resolve: {
        alias: {
            ziggyjs: path.resolve('vendor/tightenco/ziggy/dist/index.js')
        }
    }
});

if (mix.inProduction()) {
    mix.version();
}
