# WordPress RAML Console Plugin
This plugin provides the ability to embedd a RAML Console into a WordPress page.

## Usage

Once the plugin is installed and enabled, a button will show up on the Page/Post Editor. When you click on the 'RAML Console' button, a window will prompt you for the
Root RAML URL that you grab from the Anypoint Platform or you have hosted on your own server.

You also can use shortcodes to display to display the RAML Console in your post.

## Shortcodes

#### RAML Console
```html
[raml-console]
```
Argument | Example | Description
--- | --- | ---
file | `[raml-console file="<Root RAML URL>"]` | Displays the RAML Console for the specified RAML file.
