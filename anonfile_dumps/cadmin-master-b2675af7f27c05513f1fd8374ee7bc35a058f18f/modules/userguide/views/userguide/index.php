<h1>User Guide</h1>

<p>The following modules have userguide pages:</p>

<?php if( ! empty($modules)): ?>

	<?php foreach($modules as $url => $options): ?>
	
		<p>
			<strong><?php echo html::anchor(Route::get('docs/guide')->uri(array('module' => $url)), $options['name'], NULL, NULL, TRUE) ?></strong> -
			<?php echo $options['description'] ?>
		</p>
	
	<?php endforeach; ?>
	
<?php else: ?>

	<p class="error">I couldn't find any modules with userguide pages.</p>

<?php endif; ?>