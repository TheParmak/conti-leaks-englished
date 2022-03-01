<div id="widget-importancevent-editor">
<?=Form::open()?>
    <?php if ( $importanceevent->loaded() ) : ?>
    <?= Form::hidden(':argEventID', $importanceevent->id); ?>
    <?php endif; ?>
    <?php if ( isset($errors) ) : ?>
        <?php foreach($errors as $item) : ?>
            <div class="alert alert-danger small"><?= $item ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <table class="table">
        <tbody>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <?= Form::select(':argName', array_combine(array_keys($importanceEventNames), array_keys($importanceEventNames)), Arr::get($_POST, ':argName', $importanceevent->loaded() ? $importanceevent->name : null), ['class' => 'form-control selectpicker', 'data-importance-event-names' => json_encode($importanceEventNames)]); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Command
                </td>
                <td>
                    <?= Form::input(':argCommand', Arr::get($_POST, ':argCommand', $importanceevent->loaded() ? $importanceevent->command : null), ['class' => 'form-control', 'type' => 'number']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Params
                </td>
                <td>
                    <?= Form::input(':argParams', Arr::get($_POST, ':argParams', $importanceevent->loaded() ? $importanceevent->params : null), array('class' => 'form-control')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Count
                </td>
                <td>
                    <?= Form::input(':argCount', Arr::get($_POST, ':argCount', $importanceevent->loaded() ? $importanceevent->count : null), array('class' => 'form-control', 'type' => 'number')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Priority
                </td>
                <td>
                    <?= Form::input(':argPriority', Arr::get($_POST, ':argPriority', $importanceevent->loaded() ? $importanceevent->priority : null), array('class' => 'form-control', 'type' => 'number', 'required')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Add
                </td>
                <td>
                    <?= Form::input(':argAdd', Arr::get($_POST, ':argAdd', $importanceevent->loaded() ? $importanceevent->add : null), array('class' => 'form-control', 'type' => 'number')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Mul
                </td>
                <td>
                    <?= Form::input(':argMul', Arr::get($_POST, ':argMul', $importanceevent->loaded() ? $importanceevent->mul : null), array('class' => 'form-control', 'type' => 'number', 'step' => 'any')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Const
                </td>
                <td>
                    <?= Form::input(':argConst', Arr::get($_POST, ':argConst', $importanceevent->loaded() ? $importanceevent->const : ''), array('class' => 'form-control', 'type' => 'number')); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Enabled
                </td>
                <td>
                    <?= Form::checkbox(':argEnabled', '1', '1' == Arr::get($_POST, ':argEnabled', '1')); ?>
                </td>
            </tr>
        </tbody>
		<tfoot>
			<tr>
				<td colspan="2">
					<button type="submit" name="apply" class="btn-primary btn pull-right">Apply</button>
				</td>
			</tr>
		</tfoot>
	</table>
<?=Form::close()?>
</div>
            
<script>
    $(document).ready(function() {
        var $scopeWidgetImportanceeventEditor = $('#widget-importancevent-editor');
        var $selectName = $('select[name=":argName"]');
        var $inputCommand = $('input[name=":argCommand"]');

        $('.selectpicker', $scopeWidgetImportanceeventEditor).selectpicker();
        
        $selectName.on('change', function() {
            $inputCommand.val($selectName.data('importanceEventNames')[$(this).val()]);
        });
    });
</script>
