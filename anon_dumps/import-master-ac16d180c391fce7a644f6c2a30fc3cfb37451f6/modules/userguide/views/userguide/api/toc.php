<h1><?php echo 'Available Classes' ?></h1>

<label>Filter:</label>
<input type="text" id="kodoc-api-filter-box" />

<script type="text/javascript">
(function($) {
	$.fn.extend({
		api_filter: function(api_container_selector){
			var $api_container = $(api_container_selector);
			var $this = this;

			if ($api_container.length) {
				var $classes = $('.class', $api_container);
				var $methods = $('.methods li', $classes);
				var text = $methods.map(function(){ return $(this).text(); });
				var timeout = null;

				this.keyup(function(){
					clearTimeout(timeout);
					timeout = setTimeout(filter_content, 300);
				});

				filter_content();
			}

			function filter_content(){
				var search = $this.val();
				var search_regex = new RegExp(search,'gi');

				if (search == '') {
					$methods.show();
					$classes.show();
				} else {
					$classes.hide();
					$methods.hide();

					text.each(function(i){
						if (this.match(search_regex)) {
							$($methods[i]).show().closest('.class').show();
						}
					});
				}
			}

			return this;
		}
	});

	$(document).ready(function(){
		$('#kodoc-api-filter-box').api_filter('#kodoc-body').focus();
	});
})(jQuery);
</script>

<div class="class-list">

	<?php foreach ($classes as $class => $methods): $link = $route->uri(array('class' => $class)) ?>
	<div class="class <?php echo Text::alternate('left', 'right') ?>">
		<h2><?php echo HTML::anchor($link, $class, NULL, NULL, TRUE) ?></h2>
		<ul class="methods">
		<?php foreach ($methods as $method): ?>
			<li><?php echo HTML::anchor("{$link}#{$method}", "{$class}::{$method}", NULL, NULL, TRUE) ?></li>
		<?php endforeach ?>
		</ul>
	</div>
	<?php endforeach ?>

</div>
