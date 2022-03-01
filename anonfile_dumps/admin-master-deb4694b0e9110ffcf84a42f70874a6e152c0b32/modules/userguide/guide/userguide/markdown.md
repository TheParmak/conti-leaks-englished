# Markdown Syntax

The userguide uses [Markdown](http://daringfireball.net/projects/markdown/) and [Markdown Extra](http://michelf.com/projects/php-markdown/extra/) for the userguide pages, and the in-code comments used to generate the API browser.  This is a brief summary of most of Markdown and Markdown extra features.  It does not cover everything, and it does not cover all the caveats.

[!!] Be sure to check out the **[Userguide Specific Syntax](#userguide-specific-syntax)** for things that Userguide adds to markdown.

## Headers

    # Header 1
	
	## Header 2
	
	### Header 3
	
	#### Header 4

## Paragraphs
~~~
Regular text will be transformed into paragraphs.
Single returns will not make a new paragraph, this
allows for wrapping (especially for in-code
comments).

A new paragraph will start if there is a blank line between
blocks of text.  Chars like > and & are escaped for you.

To make a line break,  
put two spaces at the  
end of a line.
~~~
Regular text will be transformed into paragraphs.
Single returns will not make a new paragraph, this
allows for wrapping (especially for in-code
comments).

A new paragraph will start if there is a blank line between
blocks of text.  Chars like > and & are escaped for you.

To make a line break,  
put two spaces at the  
end of a line.

## Links
~~~
This is a normal link: [Kohana](http://kohanaframework.org).

This link has a title: [Kohana](http://kohanaframework.org "The swift PHP framework")
~~~
This is a normal link: [Kohana](http://kohanaframework.org)

This link has a title: [Kohana](http://kohanaframework.org "The swift PHP framework")

## Code blocks

	For inline code simply surround some `text with tick marks.`
	
For inline code simply surround some `text with tick marks.`

	// For a block of code,
	// indent in four spaces,
	// or with a tab

You can also do a "fenced" code block:

	~~~
	A fenced code block has tildes
	          above and below it
	This is sometimes useful when code is near lists
	~~~
~~~
A fenced code block has tildes
		  above and below it
This is sometimes useful when code is near lists
~~~

## Unordered Lists

~~~
*  To make a unordered list, put an asterisk, minus, or + at the beginning
-  of each line, surrounded by spaces.  You can mix * - and +, but it
+  makes no difference.
~~~
*  To make a unordered list, put an asterisk, minus, or + at the beginning
-  of each line, surrounded by spaces.  You can mix * - and +, but it
+  makes no difference.

## Ordered Lists

~~~
1.  For ordered lists, put a number and a period
2.  On each line that you want numbered.
9.  It doesn't actually have to be the correct number order
5.  Just as long as each line has a number
~~~
1.  For ordered lists, put a number and a period
2.  On each line that you want numbered.
9.  It doesn't actually have to be the correct number order
5.  Just as long as each line has a number

## Nested Lists

~~~
*  To nest lists you just add four spaces before the * or number
	1. Like this
		*  It's pretty basic, this line has eight spaces, so its nested twice
	1. And this line is back to the second level
		*  Out to third level again
*  And back to the first level
~~~
*  To nest lists you just add four spaces before the * or number
	1. Like this
		*  It's pretty basic, this line has eight spaces, so its nested twice
	1. And this line is back to the second level
		*  Out to third level again
*  And back to the first level

## Italics and Bold

~~~
Surround text you want *italics* with *asterisks* or _underscores_.

**Double asterisks** or __double underscores__ makes text bold.

***Triple*** will do *both at the same **time***.
~~~
Surround text you want *italics* with *asterisks* or _underscores_.

**Double asterisks** or __double underscores__ makes text **bold**.

___Triple___ will do *both at the same **time***.

## Horizontal Rules

Horizontal rules are made by placing 3 or more hyphens, asterisks, or underscores on a line by themselves.
~~~
---
* * * *
_____________________
~~~
---
* * * *
_____________________

## Images

Image syntax looks like this:

	![Alt text](/path/to/img.jpg)
	
	![Alt text](/path/to/img.jpg "Optional title")

[!!] Note that the images in userguide are [namespaced](#namespacing).

## Tables
~~~
First Header  | Second Header
------------- | -------------
Content Cell  | Content Cell
Content Cell  | Content Cell
~~~

First Header  | Second Header
------------- | -------------
Content Cell  | Content Cell
Content Cell  | Content Cell

Note that the pipes on the very left and very right side are optional, and you can change the text-alignment by adding a colon on the right, or on both sides for center.
~~~
| Item      | Value | Savings |
| --------- | -----:|:-------:|
| Computer  | $1600 |   40%   |
| Phone     |   $12 |   30%   |
| Pipe      |    $1 |    0%   |
~~~
| Item      | Value | Savings |
| --------- | -----:|:-------:|
| Computer  | $1600 |   40%   |
| Phone     |   $12 |   30%   |
| Pipe      |    $1 |    0%   |

# Userguide Specific Syntax

In addition to the features and syntax of [Markdown](http://daringfireball.net/projects/markdown/) and [Markdown Extra](http://michelf.com/projects/php-markdown/extra/) the following apply to userguide pages and api documentation:

## Namespacing

The first thing to note is that all links are "namespaced" to the current module.  For example, from anywhere within the Kohana core docs you do not need to include `kohana` at the beginning of a link url.  For example: `[Hello World Tutorial](tutorials/hello-world)` rather than `(kohana/tutorials/hello-world)`.

To link to a modules index page, have an empty url like: `[Kohana]()`.

To link to page in a different module, prefix your url with `../` and the module name.  For example: `[Kohana Routes](../kohana/routing)`

**Images are also namespaced**, using `![Alt Text](imagename.jpg)` would look for `media/guide/<modulename>/imagename.jpg`.

[!!] If you want your userguide pages to be browsable on github or similar sites outside Kohana's own userguide module, specify the optional .md file extension in your links

## API Links

You can make links to the api browser by wrapping any class name in brackets.  You may also include a function name, or propery name to link to that specifically.  All of the following will link to the API browser:

	[Request]  
	[Request::execute]  
	[Request::execute()]  
	[Request::$status]  

[Request]  
[Request::execute]  
[Request::execute()]  
[Request::$status]  

If you want to have parameters and have the function be clickable, only put the brackets around the class and function (not the params), and put a backslash in front of the opening parenthesis.

	[Kohana::$config]\('foobar','baz')
	
[Kohana::$config]\('foobar','baz')

## Notes

If you put `[!!]` in front of a line it will be a "note" and be placed in a box with a lightbulb.

	[!!]  This is a note

will display as:
	
[!!] This is a note

## Headers automatically get IDs

Headers are automatically assigned an id, based on the content of the header, so each header can be linked to. You can manually assign a different id using the syntax as defined in Markdown Extra. If multiple headers have the same content (e.g. more than one "Examples" header), only the first will get be automatically assigned an id, so you should manually assign more descriptive ids. For example:

	### Examples     {#more-descriptive-id}

## Including Views

If you need you may include a regular Kohana View file by placing the name of the view in double curly brackets.  **If the view is not found, no error or exception will be shown, the curly brackets and view name will simply remain there!**

	{{some/view/file}}
