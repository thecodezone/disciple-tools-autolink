let mix = require('laravel-mix');
const {browserSync} = require("laravel-mix");

mix.setPublicPath('dist')
  .js('magic-link/magic-link.js', 'dist/magic-link.js')
  .postCss('magic-link/magic-link.css', 'dist/magic-link.css')
  .browserSync({
    proxy: "https://discipletools.test",
    files: [
      'dist/*.js',
      'dist/*.css',
      'magic-link/templates/**/*.php',
    ]
  })
