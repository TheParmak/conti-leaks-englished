<h2>Modules</h2>

<?php if( ! empty($modules)): ?>

	<ul>
	<?php foreach($modules as $url => $options): ?>
	
		<li><?php echo html::anchor(Route::get('docs/guide')->uri(array('module' => $url)), $options['name'], NULL, NULL, TRUE) ?></li>
	
	<?php endforeach; ?>
	</ul>

<?php else: ?>

	<p class="error">No modules.</p>

<?php endif; ?>