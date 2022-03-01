<?php if (is_array($array)): ?>
<div class="page-toc">
	<?php foreach ($array as $item): ?>
		<?php if ($item['level'] > 1): ?>
		<?php echo str_repeat('&nbsp;', ($item['level'] - 1) * 4) ?>
		<?php endif ?>
		<?php echo HTML::anchor('#'.$item['id'],$item['name'], NULL, NULL, TRUE); ?><br />
	<?php endforeach; ?>
</div>
<?php endif ?>
