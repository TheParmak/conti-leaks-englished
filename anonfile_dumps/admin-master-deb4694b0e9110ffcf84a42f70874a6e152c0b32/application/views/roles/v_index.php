<table class="table">
	<thead>
		<tr>
			<td>Role</td>
			<td>Actions</td>
			<td></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($roles as $role){?>
			<?php if($role->id != 1){?>
				<tr>
					<td>
						<?php echo $role->name?>
					</td>
					<td>
						<ul>
							<?php foreach($role->actions->find_all() as $action){?>
								<li>
									<?php echo $action->description ?>
								</li>
							<?php }?>
						</ul>
					</td>
					<td>
						<a class="btn btn-primary pull-right" href="/roles/editor/<?php echo $role->id?>">
							<span class="glyphicon glyphicon-edit"></span>
						</a>
					</td>
				</tr>
			<?php }?>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<a href="/roles/editor/" class="btn btn-success pull-right">
					<span class="glyphicon glyphicon-plus"></span>
				</a>
			</td>
		</tr>
	</tfoot>
</table>