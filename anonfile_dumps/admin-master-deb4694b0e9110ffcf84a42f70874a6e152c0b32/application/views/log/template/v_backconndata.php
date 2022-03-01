<?if($backconndata->count()){?>
	<table class="table table-bordered">
		<thead style="font-weight: bold">
			<tr>
				<td colspan="5">BC log</td>
			</tr>
			<tr>
				<td>Name</td>
				<td>Ip</td>
				<td>Port</td>
				<td>Operation</td>
				<td>Datetime</td>
			</tr>
		</thead>
		<tbody>
		<?foreach($backconndata as $item){?>
			<tr>
				<td>
					<?php echo $item->name ?>
				</td>
				<td>
					<?php echo $item->ip ?>
				</td>
				<td>
					<?php echo $item->port ?>
				</td>
				<td>
					<?php echo $item->operation ?>
				</td>
				<td>
					<?php echo $item->datetime ?>
				</td>
			</tr>
		<?}?>
		</tbody>
	</table>

    <?php if ( Helper::checkActionInRole('Remove') ) : ?>
        <div class="clearfix" style="margin-bottom: 20px;">
            <?= Form::open('/remove?redirect_to=https://' . $_SERVER['HTTP_HOST'] . '/log/' . $client->clientid); ?>
                <?= Form::hidden('clientid', $client->clientid); ?>
                <button type="submit" class="btn btn-danger pull-right" name="DeleteBackConnData">DeleteBackConnData 75%</button>
            <?= Form::close(); ?>
        </div>
    <?php endif; ?>
<?}?>