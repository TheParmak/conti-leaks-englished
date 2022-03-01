<?=Form::open();?>
    <?php if ( isset($errors) ) : ?>
        <?php foreach($errors as $item) : ?>
            <div class="alert alert-danger small"><?= $item ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <table class="table">
        <tbody>
            <tr>
                <td>
                    <?=Form::input(':argName', $argName, ['class' => 'form-control', 'placeholder' => 'Name'])?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=Form::input(':argProc', null, ['class' => 'form-control', 'placeholder' => 'Proc'])?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><?=Form::button('apply', 'Apply', ['class' => 'btn btn-primary pull-right'])?></td>
            </tr>
        </tfoot>
    </table>
<?=Form::close();?>