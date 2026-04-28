let mix = require("laravel-mix");
let NovaExtension = require("laravel-nova-devtool");
let path = require("path");

mix.extend("nova", new NovaExtension());

mix
  .setPublicPath("dist")
  .js("resources/js/tool.js", "js")
  .vue({ version: 3 })
  .css("resources/css/tool.css", "css")
  .webpackConfig({
    resolveLoader: {
      modules: [path.resolve(__dirname, "node_modules"), "node_modules"]
    }
  })
  .nova("vlinde/laravel-bugster")
  .version();
