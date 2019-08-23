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

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix
    .copy('node_modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js', 'public/js/bootstrap-timepicker.min.js')
    .copy('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js', 'public/js/bootstrap-datepicker.min.js')
    .copy('node_modules/jquery-toast-plugin/dist/jquery.toast.min.js', 'public/js/jquery.toast.min.js')
    .copy('node_modules/lightgallery/dist/js/lightgallery.min.js', 'public/js/lightgallery.min.js');
