<?=Form::open()?>
    <?php if ( isset($errors) ) : ?>
        <?php foreach($errors as $item) : ?>
            <div class="alert alert-danger small"><?= $item ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
	<table class="table">
		<tbody>
			<tr>
				<td>
					<?php if(!$role->loaded()){
						echo Form::input('name', $role->name, array('class' => 'form-control', 'placeholder' => 'Name'));
					}else{
						echo Form::input('name', $role->name, array('class' => 'form-control', 'disabled' => 'true', 'placeholder' => 'Name'));
					} ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php foreach($actions as $action){ ?>
						<div class="checkbox">
							<label>
								<?=Form::checkbox('check[]', $action->id, $role->has('actions', $action))?>
								<?=$action->name?><?php if ( $action->description != $action->name ) : ?> (<?= $action->description; ?>)<?php endif; ?>
							</label>
						</div>
					<?php } ?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<button class="btn-primary btn pull-right" type="submit" name="apply">Apply</button>
				</td>
			</tr>
		</tfoot>
	</table>
<?=Form::close()?>