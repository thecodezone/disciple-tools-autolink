let mix = require('laravel-mix');
const {browserSync} = require("laravel-mix");

mix.setPublicPath('dist')
  .setResourceRoot('magic-link')
  .js('magic-link/magic-link.js', 'dist')
  .postCss('magic-link/magic-link.css', 'dist')
  .browserSync("https://discipletools.test")
