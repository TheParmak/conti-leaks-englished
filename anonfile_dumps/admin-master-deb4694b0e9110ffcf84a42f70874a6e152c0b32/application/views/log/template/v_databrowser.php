<?if($databrowser->count()){?>
	<table class="table table-bordered">
		<thead>
			<tr>
                <th colspan="3">Databrowser</th>
            </tr>
			<tr>
				<th class="col-md-1">Id</th>
				<th>File</th>
				<th>Datetime</th>
			</tr>
		</thead>
		<tbody>
		<?foreach($databrowser as $item){?>
			<tr>
				<td>
					<?php echo $item->id ?>
				</td>
				<td>
					<a class="btn btn-primary" href="/download/databrowser/<?php echo $item->id ?>">
						<span class="glyphicon glyphicon-download-alt"></span>
					</a>
				</td>
				<td>
					<?php echo $item->datetime ?>
				</td>
			</tr>
		<?}?>
		</tbody>
	</table>
<?}?>

<div class="panel panel-body">
    <?=$link?>
</div>