const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.js('resources/js/app.js', 'public/js/app.js')
mix.js('resources/js/store/index.js', 'public/js/app.js')
mix.sass('resources/sass/app.scss', 'public/css/app.scss')
mix.combine([
    'resources/css/custom.css',
    'resources/css/vuetify.css'
],'public/css/app.css')

mix.disableSuccessNotifications()
