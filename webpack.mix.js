const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js("resources/js/app.js", "public/assets/js")
  .postCss("resources/css/app.css", "public/assets/css", [
    require("tailwindcss"),
  ])
  .copyDirectory("resources/images", "public/assets/images")
  .version();

// Hot reloading
if (mix.inProduction()) {
  mix.version();
} else {
  mix.sourceMaps();
}
