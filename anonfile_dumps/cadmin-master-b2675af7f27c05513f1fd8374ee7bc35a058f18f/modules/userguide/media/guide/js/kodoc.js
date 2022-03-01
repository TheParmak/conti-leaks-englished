$(document).ready(function()
{

// Syntax highlighter

	$('pre:not(.debug) code').each(function()
	{
		$(this).addClass('brush: php, class-name: highlighted');
	});

	SyntaxHighlighter.config.tagName = 'code';
	// Don't show the toolbar or line-numbers.
	SyntaxHighlighter.defaults.gutter = false;
	SyntaxHighlighter.all();
	
	// Any link that has the current page as its href should be class="current"
	$('a[href="'+ window.location.pathname +'"]').addClass('current');

	// Breadcrumbs magic
	$('#kodoc-breadcrumb li.last').each(function()
	{
		var $this = $(this);
		var $topics = $('#kodoc-topics li').has('a.current').slice(0, -1);

		$topics.each(function()
		{
			// Create a copy of the menu link
			var $crumb = $(this).children('a:first, span:not(.toggle):first').clone();

			// Insert the menu link into the breadcrumbs
			$('<li></li>').html($crumb).insertBefore($this);
		});
	});

	// Collapsing menus
	$('#kodoc-topics li:has(li)').each(function()
	{
		var $this = $(this);
		var toggle = $('<span class="toggle"></span>');
		var menu = $this.find('>ul,>ol');

		toggle.click(function()
		{
			if (menu.is(':visible'))
			{
				menu.stop(true, true).slideUp('fast');
				toggle.html('+');
			}
			else
			{
				menu.stop(true, true).slideDown('fast');
				toggle.html('&ndash;');
			}
		});

		$this.find('>span').click(function()
		{
			// Menu without a link
			toggle.click();
		});

		if ( ! $this.is(':has(a.current)'))
		{
			menu.hide();
		}

		toggle.html(menu.is(':visible') ? '&ndash;' : '+').prependTo($this);
	});

// Show source links

	$('#kodoc-main .method-source').each(function()
	{
		var self = $(this);
		var togg = $(' <a class="sourcecode-toggle">[show]</a>').appendTo($('h4', self));
		var code = self.find('pre').hide();

		togg.toggle(function()
		{
			togg.html('[hide]');
			code.stop(true, true).slideDown();
		},
		function()
		{
			togg.html('[show]');
			code.stop(true, true).slideUp();
		});
	});

	// "Link to this" link that appears when you hover over a header
	$('#kodoc-body')
		.find('h1[id], h2[id], h3[id], h4[id], h5[id], h6[id]')
		.append(function(){
			var $this = $(this);
			return '<a href="#' + $this.attr('id') + '" class="permalink">link to this</a>';
		});
});
