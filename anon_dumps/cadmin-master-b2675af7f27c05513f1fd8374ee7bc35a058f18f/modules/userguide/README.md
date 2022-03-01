## What needs to be done?

Most articles are stubs, with a couple links to pages to be used as a reference when writing the page.  The idea is to use the information on those links to help write the new ones.  Some of the old userguide pages can probably be mostly copied, with a few improvements, others will be better to be completely rewritten.  If you ever have questions, please feel free to jump in the kohana irc channel.

## Guidelines

Documentation should use complete sentences, good grammar, and be as clear as possible.  Use lots of example code, but make sure the examples follow the Kohana conventions and style.

Try to commit often, with each commit only changing a file or two, rather than changing a ton of files and commiting it all at once.  This will make it easier to offer feedback and merge your changes.   Make sure your commit messages are clear and descriptive.  Good: "Added initial draft of hello world tutorial."  Bad: "working on docs".

If you feel a menu needs to be rearranged or a module needs new pages, please open a [bug report](http://dev.kohanaframework.org/projects/userguide3/issues/new) to discuss it.

## A brief explanation of how the userguide works:

The userguide uses [Markdown](http://daringfireball.net/projects/markdown/) and [Markdown Extra](http://michelf.com/projects/php-markdown/extra/) for the documentation.  Here is a short intro to [Markdown syntax](http://kohanut.com/docs/using.markdown), as well as the [complete guide](http://daringfireball.net/projects/markdown/syntax), and the things [Markdown Extra adds](http://michelf.com/projects/php-markdown/extra/).  Also read what the userguide adds to markdown at the end of this readme.

### Userguide pages

Userguide pages are in the module they apply to, in `guide/<module>`. Documentation for Kohana is in `system/guide/kohana` and documentation for orm is in `modules/orm/guide/orm`, etc.

Each module has an index page at `guide/<module>/index.md`.

Each module's menu is in `guide/<module>/menu.md`. 

### Images

Any images used in the userguide pages must be in `media/guide/<module>/`.  For example, if a userguide page has `![Image Title](hello-world.jpg)` the image would be located at `media/guide/<module>/hello-world.jpg`.  Images for the ORM module are in `modules/orm/media/guide/orm`, and images for the Kohana docs are in `system/media/guide/kohana`.

### API browser

The API browser is generated from the actual source code.  The descriptions for classes, constants, properties, and methods is extracted from the comments and parsed in Markdown.  For example if you look in the comment for [Kohana_Core::init](http://github.com/kohana/core/blob/c443c44922ef13421f4a/classes/kohana/core.php#L5) you can see a markdown list and table.  These are parsed and show correctly in the API browser.  `@param`, `@uses`, `@throws`, `@returns` and other tags are parsed as well.

# What the userguide adds to markdown:

In addition to the features and syntax of [Markdown](http://daringfireball.net/projects/markdown/) and [Markdown Extra](http://michelf.com/projects/php-markdown/extra/) the following apply to userguide pages and api documentation:

### Namespacing

The first thing to note is that all urls are "namespaced". The name of the module is automatically added to links and image urls, you do not need to include it.  For example, to link to the hello world tutorial page from another page in the Kohana userguide, you should use `[Hello World Tutorial](tutorials/hello-world)` rather than `(kohana/tutorials/hello-world)`.  To link to pages in a different section of the guide, you can use `../`, for example `[Cache](../cache/usage)`.

### Notes

If you put [!!] in front of line it will be a note, put in a box with a lightbulb.

    [!!] This is a note.

### Headers automatically get IDs

Headers are automatically assigned an id, based on the content of the header, so each header can be linked to.  You can manually assign a different id using the syntax as defined in Markdown Extra.  If multiple headers have the same content, like if more than one header is "Examples", only the first will get be automatically assigned an id, so you should manually assign more descriptive ids.  For example:

    ### Examples     {#header-id-examples}

### API links

You can make links to the api browser by wrapping any class name in brackets.  You may also include a function and it will link to that function.  All of the following will link to the API browser:

    [Request]
	[Request::factory]
	[Request::factory()]

If you want to have parameters, only put the brackets around the class and function (not the params), and put a backslash in front of the opening parenthesis. 

	[Kohana::$config]\('foobar','baz')

### Including Views

You may include a view by putting the name of the view in double curly brackets.  **If the view is not found, no exception or error will be shown!** The curly brackets and view will simply be shown an the page as is.

    {{some/view}}