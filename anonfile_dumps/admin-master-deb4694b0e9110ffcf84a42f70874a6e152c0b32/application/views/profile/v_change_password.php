<?= Form::open(); ?>
    <?php if ( isset($errors) ) : ?>
        <?php foreach($errors as $item) : ?>
            <div class="alert alert-danger small"><?= $item ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
	<table class="table">
		<tbody>
			<tr>
				<td>Current password</td>
				<td>
					<?= Form::password('password_current', null, array('class' => 'form-control')); ?>
				</td>
			</tr>
			<tr>
				<td>New password</td>
				<td>
					<?= Form::password('password', null, array('class' => 'form-control')); ?>
				</td>
			</tr>
			<tr>
				<td>New password confirm</td>
				<td>
					<?= Form::password('password_confirm', null, array('class' => 'form-control')); ?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
					<button class="btn btn-primary pull-right" type="submit" name="change">Apply</button>
				</td>
			</tr>
		</tfoot>
	</table>
<?= Form::close(); ?>