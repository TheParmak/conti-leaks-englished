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
                    <?=Form::input(':argName', null, ['class' => 'form-control', 'type' => 'text', 'placeholder' => 'Name'])?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=Form::input(':argPassword', null, ['class' => 'form-control', 'type' => 'text', 'placeholder' => 'Password'])?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <?=Form::button('apply', 'Apply', ['class' => 'btn btn-primary pull-right', 'type' => 'submit'])?>
                </td>
            </tr>
        </tfoot>
    </table>
<?=Form::close()?>