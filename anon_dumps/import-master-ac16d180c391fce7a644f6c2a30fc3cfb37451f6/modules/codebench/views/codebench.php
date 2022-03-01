<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Codebench — A benchmarking module.
 *
 * @package    Kohana/Codebench
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
?>
<!DOCTYPE html>

<html lang="en">
<head>

	<meta charset="utf-8" />
	<title><?php if ($class !== ''): ?>
			<?php echo $class, ' · ' ?>
		<?php endif; ?>Codebench</title>

	<style>
		/* General styles*/
		body { position:relative; margin:1em 2em; font:12px monaco,monospace; }
		h1 { font-size:24px; letter-spacing:-0.05em; }
		h2 { font-size:18px; letter-spacing:-0.1em; }
		input, code { font:inherit; }
		code { background:#e5e5e5; }
		caption { display:none; }

		/* Form */
		#runner { margin-bottom:2em; }
		#runner input[type="text"] { letter-spacing:-0.05em; }

		/* Expand/Collapse all */
		#toggle_all { position:absolute; top:0; right:0; margin:0; padding:0 4px; background:#000; font-size:18px; color:#fff; cursor:pointer; -moz-border-radius:2px; -webkit-border-radius:2px; }

		/* Benchmark main graphs */
		#bench { margin:2em 0; padding:0; list-style:none; }
		#bench > li { margin:6px 0; }
		#bench h2 { position:relative; margin:0; padding:2px; background:#ccc; border:1px solid #999; cursor:pointer; -moz-border-radius:3px; -webkit-border-radius:3px; }
		#bench h2 > span { display:block; min-width:1px; height:33px; background:#fff; -moz-border-radius:2px; -webkit-border-radius:2px; }
		#bench h2 .method { position:absolute; top:6px; left:8px; text-shadow:0 -1px 0 rgba(255,255,255,0.6); }
		#bench h2 .method:before { content:'▸ '; }
		#bench h2 .percent { position:absolute; top:6px; right:6px; padding:0 4px; background:#000; color:#fff; font-weight:normal; letter-spacing:0; -moz-border-radius:2px; -webkit-border-radius:2px; }
		#bench h2:hover .method { left:10px; }
		#bench h2.expanded { margin:12px 0 0; -moz-border-radius-bottomleft:0; -moz-border-radius-bottomright:0; -webkit-border-bottom-left-radius:0; -webkit-border-bottom-right-radius:0; }
		#bench h2.expanded .method:before { content:'▾ '; }

		/* Colorization of the bars */
		#bench .grade-A { background:#3f0; }
		#bench .grade-B { background:#fc0; }
		#bench .grade-C { background:#f90; }
		#bench .grade-D { background:#f60; }
		#bench .grade-E { background:#f30; }
		#bench .grade-F { background:#f00; }

		/* Benchmark details */
		#bench > li > div { display:none; margin:0 0 12px; padding:0 0 2px; background:#eee; border:1px solid #999; border-top:0; -moz-border-radius-bottomleft:3px; -moz-border-radius-bottomright:3px; -webkit-border-bottom-left-radius:3px; -webkit-border-bottom-right-radius:3px; }
		#bench > li > div table { width:100%; background:#eee; border-collapse:collapse; }
		#bench > li > div th { padding:6px; background:#ddd url(data:image/gif;base64,R0lGODlhAQASALMAAMfHx8TExM7Oztvb29jY2NbW1tPT09DQ0MrKygAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAABABIAAAQNMAQAEBLiHGNKIcQwRAA7) repeat-x 0 1px; text-align:left; }
		#bench > li > div td { padding:6px; border-top:1px solid #ccc; vertical-align:top; }
		#bench .numeric { padding-left:18px; text-align:right; }
		#bench .numeric span { position:relative; display:block; height:16px; }
		#bench .numeric span span { position:absolute; top:0; right:0; min-width:1px; background:#ccc; -moz-border-radius:2px; -webkit-border-radius:2px; }
		#bench .numeric span span span { top:0; right:0; background:none; }
		#bench tbody tr:hover { background:#fff; }
		#bench tbody tr.highlight { background:#ffc; }

		/* Footer */
		#footer { margin-top:2em; padding-top:1em; border-top:1px solid #ccc; color:#999; }
		#footer a { color:inherit; }

		/* Misc text styles */
		.alert { padding:0 0.5em; background:#900; font-weight:normal; color:#fff; -moz-border-radius:3px; -webkit-border-radius:3px; }
		.quiet { color:#999; }
		.help { cursor:help; }
	</style>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			// Insert "Toggle All" button
			var expand_all_text   = '▸ Expand all';
			var collapse_all_text = '▾ Collapse all';
			$('#bench').before('<p id="toggle_all">'+expand_all_text+'</p>');

			// Cache these selection operations
			var $runner       = $('#runner');
			var $toggle_all   = $('#toggle_all');
			var $bench_titles = $('#bench > li > h2');
			var $bench_rows   = $('#bench > li > div > table > tbody > tr');

			// Runner form
			$(':input:first', $runner).focus();
			$runner.submit(function() {
				$(':submit', this).attr('value', 'Running…').attr('disabled', 'disabled');
				$('.alert', this).remove();
			});

			// Toggle details for all benchmarks
			$('#toggle_all').click(function() {
				if ($(this).data('expanded')) {
					$(this).data('expanded', false);
					$(this).text(expand_all_text);
					$bench_titles.removeClass('expanded').siblings().hide();
				}
				else {
					$(this).data('expanded', true);
					$(this).text(collapse_all_text);
					$bench_titles.addClass('expanded').siblings().show();
				}
			});

			<?php if (Kohana::$config->load('codebench')->expand_all) { ?>
				// Expand all benchmark details by default
				$toggle_all.click();
			<?php } ?>

			// Toggle details for a single benchmark
			$bench_titles.click(function() {
				$(this).toggleClass('expanded').siblings().toggle();

				// Counts of bench titles
				var total_bench_titles    = $bench_titles.length;
				var expanded_bench_titles = $bench_titles.filter('.expanded').length;

				// If no benchmark details are expanded, change "Collapse all" to "Expand all"
				if (expanded_bench_titles == 0 && $toggle_all.data('expanded')) {
					$toggle_all.click();
				}
				// If all benchmark details are expanded, change "Expand all" to "Collapse all"
				else if (expanded_bench_titles == total_bench_titles && ! $toggle_all.data('expanded')) {
					$toggle_all.click();
				}
			});

			// Highlight clicked rows
			$bench_rows.click(function() {
				$(this).toggleClass('highlight');
			// Highlight doubleclicked rows globally
			}).dblclick(function() {
				var nth_row = $(this).parent().children().index(this) + 1;
				if ($(this).hasClass('highlight')) {
					$bench_rows.filter(':nth-child('+nth_row+')').removeClass('highlight');
				}
				else {
					$bench_rows.filter(':nth-child('+nth_row+')').addClass('highlight');
				}
			});
		});
	</script>

</head>
<body>

	<!--[if IE]><p class="alert">This page is not meant to be viewed in Internet Explorer. Get a better browser.</p><![endif]-->

	<form id="runner" method="post" action="<?php echo URL::site('codebench') ?>">
		<h1>
			<input name="class" type="text" value="<?php echo ($class !== '') ? $class : 'Bench_' ?>" size="25" title="Name of the Codebench library to run" />
			<input type="submit" value="Run" />
			<?php if ( ! empty($class)) { ?>
				<?php if (empty($codebench)) { ?>
					<strong class="alert">Library not found</strong>
				<?php } elseif (empty($codebench['benchmarks'])) { ?>
					<strong class="alert">No methods found to benchmark</strong>
				<?php } ?>
			<?php } ?>
		</h1>
	</form>

	<?php if ( ! empty($codebench)) { ?>

		<?php if (empty($codebench['benchmarks'])) { ?>

			<p>
				<strong>
					Remember to prefix the methods you want to benchmark with “bench”.<br />
					You might also want to overwrite <code>Codebench->method_filter()</code>.
				</strong>
			</p>

		<?php } else { ?>

			<ul id="bench">
			<?php foreach ($codebench['benchmarks'] as $method => $benchmark) { ?>
				<li>

					<h2 title="<?php printf('%01.6f', $benchmark['time']) ?>s">
						<span class="grade-<?php echo $benchmark['grade']['time'] ?>" style="width:<?php echo $benchmark['percent']['slowest']['time'] ?>%">
							<span class="method"><?php echo $method ?></span>
							<span class="percent">+<?php echo (int) $benchmark['percent']['fastest']['time'] ?>%</span>
						</span>
					</h2>

					<div>
						<table>
							<caption>Benchmarks per subject for <?php echo $method ?></caption>
							<thead>
								<tr>
									<th style="width:50%">subject → return</th>
									<th class="numeric" style="width:25%" title="Total method memory"><?php echo Text::bytes($benchmark['memory'], 'MB', '%01.6f%s') ?></th>
									<th class="numeric" style="width:25%" title="Total method time"><?php printf('%01.6f', $benchmark['time']) ?>s</th>
								</tr>
							</thead>
							<tbody>

							<?php foreach ($benchmark['subjects'] as $subject_key => $subject) { ?>
								<tr>
									<td>
										<strong class="help" title="(<?php echo gettype($codebench['subjects'][$subject_key]) ?>) <?php echo HTML::chars(var_export($codebench['subjects'][$subject_key], TRUE)) ?>">
											[<?php echo HTML::chars($subject_key) ?>] →
										</strong>
										<span class="quiet">(<?php echo gettype($subject['return']) ?>)</span>
										<?php echo HTML::chars(var_export($subject['return'], TRUE)) ?>
									</td>
									<td class="numeric">
										<span title="+<?php echo (int) $subject['percent']['fastest']['memory'] ?>% memory">
											<span style="width:<?php echo $subject['percent']['slowest']['memory'] ?>%">
												<span><?php echo Text::bytes($subject['memory'], 'MB', '%01.6f%s') ?></span>
											</span>
										</span>
									</td>
									<td class="numeric">
										<span title="+<?php echo (int) $subject['percent']['fastest']['time'] ?>% time">
											<span style="width:<?php echo $subject['percent']['slowest']['time'] ?>%">
												<span><?php printf('%01.6f', $subject['time']) ?>s</span>
											</span>
										</span>
									</td>
								</tr>
							<?php } ?>

							</tbody>
						</table>
					</div>

				</li>
			<?php } ?>
			</ul>

		<?php } ?>

		<?php if ( ! empty($codebench['description'])) { ?>
			<?php echo Text::auto_p(Text::auto_link($codebench['description']), FALSE) ?>
		<?php } ?>

		<?php // echo '<h2>Raw output:</h2>', Debug::vars($codebench) ?>

	<?php } ?>

	<p id="footer">
		Page executed in <strong><?php echo round(microtime(TRUE) - KOHANA_START_TIME, 2) ?>&nbsp;s</strong>
		using <strong><?php echo Text::widont(Text::bytes(memory_get_usage(), 'MB')) ?></strong> of memory.<br />
		<a href="http://github.com/kohana/codebench">Codebench</a>, a <a href="http://kohanaframework.org/">Kohana</a> module
		by <a href="http://www.geertdedeckere.be/article/introducing-codebench">Geert De Deckere</a>.
	</p>

</body>
</html>
