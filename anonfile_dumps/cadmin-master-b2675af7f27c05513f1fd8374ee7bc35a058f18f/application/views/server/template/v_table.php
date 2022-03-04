<?php echo Form::open(); ?>
	<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<td>
				<span class="glyphicon glyphicon-check"></span>
			</td>
			<?php foreach($columns as $key => $column ){ ?>
				<td>
					<?php echo $key ?>
				</td>
			<?php } ?>
			<td></td>
		</tr>
		</thead>
		<tbody>
			<?php foreach($servers as $server){ ?>
				<tr>
					<td>
					</td>
					<td>
						<?php echo $server->name ?>
					</td>
					<td>
						<?php echo $server->ip ?>
					</td>
					<td>
						<?php echo $server->port ?>
					</td>
					<td>
						<?php echo $server->password1 ?>
					</td>
					<td>
						<?php echo $server->password2 ?>
					</td>
					<td></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php echo Form::close(); ?>