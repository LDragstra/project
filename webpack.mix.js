const mix = require("laravel-mix");

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

mix.react("resources/js/app.js", "public/js")
    .sass("resources/sass/app.scss", "public/css")
    .sass("resources/sass/toastr.scss", "public/css")
    .scripts(
        [
            "resources/js/toastr.js",
            "resources/js/bonnen.js",
            "resources/js/facturen.js",
            "resources/js/refresh.js",
            "resources/js/factoring.js"
        ],
        "public/js/index.js"
    );
