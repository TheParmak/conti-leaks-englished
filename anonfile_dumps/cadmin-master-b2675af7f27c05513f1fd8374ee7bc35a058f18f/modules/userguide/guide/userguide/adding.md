# Adding your module to the userguide

Making your module work with the userguide is simple.

First, copy this config and place in it `<module>/config/userguide.php`, replacing anything in `<>` with the appropriate things:

	return array
	(
		// Leave this alone
		'modules' => array(
	
			// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
			'<modulename>' => array(
	
				// Whether this modules userguide pages should be shown
				'enabled' => TRUE,
				
				// The name that should show up on the userguide index page
				'name' => '<Module Name>',
	
				// A short description of this module, shown on the index page
				'description' => '<Description goes here.>',
				
				// Copyright message, shown in the footer for this module
				'copyright' => '&copy; 2010–2011 <Your Name>',
			)	
		),

		/*
		 * If you use transparent extension outside the Kohana_ namespace,
		 * add your class prefix here. Both common Kohana naming conventions are
		 * excluded: 
		 *   - Modulename extends Modulename_Core
		 *   - Foo extends Modulename_Foo
		 * 
		 * For example, if you use Modulename_<class_name> for your base classes
		 * then you would define:
		 */
		'transparent_prefixes' => array(
			'Modulename' => TRUE,
		)
	);

Next, create a folder in your module directory called `guide/<modulename>` and create `index.md` and `menu.md`.  All userguide pages use [Markdown](markdown).  The index page is what is shown on the index of your module, the menu is what shows up in the side column.  The menu should be formatted like this:

	## [Module Name]()
	 - [Page name](page-path)
	 - [This is a Category](category)
		 - [Sub Page](category/sub-page)
		 - [Another](category/another)
			 - [Sub sub page](category/another/sub-page)
	 - Categories do not have to be a link to a page
		 - [Etcetera](etc)

Page paths are relative to `guide/<modulename>`.  So `[Page name](path-path)` would look for `guide/<modulename>/page-name.md` and `[Another](category/another)` would look for `guide/<modulename>/page-name.md`.   The guide pages can be named or arranged any way you want within that folder (with the exception of `menu.md` and `index.md`). The breadcrumbs and page titles are pulled from the `menu.md file`, not the file names or paths.  You can have items that are not pages (a category that doesn't have a corresponding page).  To link to the `index.md` page, you should have an empty link, e.g. `[Module Name]()`.  Do not include `.md` in your links.  