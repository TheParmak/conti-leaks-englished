# How the Userguide works

The userguide uses [Markdown](markdown) for the documentation.  Both the userguide pages, and the in code comments for the API browser are written in markdown.

## Userguide pages

Userguide pages are in the module they apply to, in `guide/<module>`. For example, documentation for Kohana is in `system/guide/kohana` and documentation for orm is in `modules/orm/guide/orm`, database is in `modules/database/guide/database`, etc.

Each module has an index page at `guide/<module>/index.md`.

Each module's menu is at `guide/<module>/menu.md`.

All other pages are are in `guide/<module>` and can be organized in subfolders and named however you want.

For more info on how to make your module have userguide pages, see [Adding your module](adding).

### Images

Any images used in the userguide pages must be in `media/guide/<module>/`.  For example, if a page has `![Image Title](hello-world.jpg)` the image would be located at `media/guide/<module>/hello-world.jpg`.  Images for the ORM module are in `modules/orm/media/guide/orm`, and images for the Kohana docs are in `system/media/guide/kohana`.

### API browser

The API browser is generated from the actual source code.  The descriptions for classes, constants, properties, and methods is extracted from the comments and parsed in Markdown.  For example if you look in the comment for [Kohana_Core::init](http://github.com/kohana/core/blob/c443c44922ef13421f4a/classes/kohana/core.php#L5) you can see a markdown list and table.  These are parsed and show correctly in the API browser.  `@param`, `@uses`, `@throws`, `@returns` and other tags are parsed as well.

TODO: give more specific details on how to comment your classes, constants, methods, etc. including package and how it relates to the api module.
