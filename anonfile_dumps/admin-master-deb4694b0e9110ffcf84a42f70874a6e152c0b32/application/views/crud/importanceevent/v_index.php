<?= Form::open(); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<td>
				<span class="glyphicon glyphicon-check"></span>
			</td>
			<td>
				Name
			</td>
			<td>
				Command
			</td>
			<td>
				Params
			</td>
			<td>
				Count
			</td>
			<td>
				Priority
			</td>
			<td>
				Add
			</td>
			<td>
				Mul
			</td>
			<td>
				Const
			</td>
            <td style="width: 180px;">
                &nbsp;
            </td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($importanceevents as $item){ ?>
			<tr>
				<td>
					<input class="argID" type="checkbox" name="check[][:argEventID]" value="<?= $item['id']; ?>">
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['name']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['command']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['params']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['count']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['priority']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['add']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['mul']; ?>
				</td>
				<td<?php if (!$item['enabled']) : ?> class="text-muted"<?php endif; ?>>
					<?= $item['const']; ?>
				</td>
                <td>
                    <a class="btn btn-primary pull-right" style="margin-left: 10px;" href="/crud/importanceevent/editor/<?= $item['id']; ?>">
                        Edit <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <button class="btn btn-<?= $item['enabled'] ? 'danger' : 'success'; ?> pull-right" style="margin-left: 10px;" name="change[<?= $item['enabled'] ? 'disable' : 'enable'; ?>][<?= $item['id']; ?>]" type="submit">
                        <?= $item['enabled'] ? 'Disable' : 'Enable'; ?>
                    </button>
                </td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="13">
			<input type="checkbox" id="select_all">
            <a class="btn btn-primary pull-right" style="margin-left: 10px;" href="/crud/importanceevent/editor/">
                Add Importance Event
            </a>
			<button type="submit" name="deleteImportanceEvents" title="Delete" class="btn btn-danger pull-right" style="margin-left: 10px;">
				<span class="glyphicon glyphicon-trash"></span>
			</button>
		</td>
	</tr>
	</tfoot>
</table>
<?= Form::close(); ?>

<script>
    $(document).ready(function() {
        $('#select_all').click(function(event){
            if(this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            }else{
                $(':checkbox').each(function(){
                    this.checked = false;
                });
            }
        });
    });
</script>