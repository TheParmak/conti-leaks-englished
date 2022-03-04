<div class="row">
	<div class="col-md-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<td>Program</td>
					<td>Count</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($programs as $item){?>
					<tr>
						<td><?php echo $item->param ?></td>
						<td class="col-md-1"><?php echo $item->value ?></td>
					</tr>
				<?}?>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<td>Browser</td>
					<td>Count</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($browser as $item){?>
					<tr>
						<td><?php echo $item->param ?></td>
						<td class="col-md-1"><?php echo $item->value ?></td>
					</tr>
				<?}?>
			</tbody>
		</table>

		<table class="table table-bordered">
			<thead>
				<tr>
					<td>System</td>
					<td>Count</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($clients as $item){?>
					<tr>
						<td><?php echo $item->param ?></td>
						<td class="col-md-1"><?php echo $item->value ?></td>
					</tr>
				<?}?>
			</tbody>
		</table>

		<?if($vars->count()){?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<td>SYSTEM/NAT</td>
						<td>%</td>
					</tr>
				</thead>
				<tbody>
					<?foreach($vars as $item){?>
						<tr>
							<td>
								<?php echo $item->param ?>
							</td>
							<td class="col-md-1">
								<?php echo round($item->value / ($vars_count / 100)) ?>
							</td>
						</tr>
					<?}?>
				</tbody>
			</table>
		<?}?>
	</div>
</div>