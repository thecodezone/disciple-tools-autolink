let mix = require("laravel-mix");
const { browserSync } = require("laravel-mix");
require("dotenv").config();

mix.webpackConfig({
  output: {
    publicPath: "/wp-content/plugins/disciple-tools-autolink/dist/",
  },
});

mix
  .setPublicPath("dist")
  .js("magic-link/magic-link.js", "dist/magic-link.js")
  .sourceMaps()
  .postCss("magic-link/magic-link.css", "dist/magic-link.css")
  .browserSync({
    proxy: process.env.MIX_URL,
    files: ["dist/*.js", "dist/*.css", "magic-link/templates/**/*.php"],
  });
