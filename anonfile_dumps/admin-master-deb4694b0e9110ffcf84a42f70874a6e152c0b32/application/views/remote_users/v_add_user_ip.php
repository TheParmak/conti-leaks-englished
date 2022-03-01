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
                    <?=Form::input(':argName', $argName, ['class' => 'form-control'])?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=Form::input(':argAddrFrom', null, ['class' => 'form-control', 'placeholder' => 'From'])?>
                </td>
            </tr>
            <tr>
                <td>
                    <?=Form::input(':argAddrTo', null, ['class' => 'form-control', 'placeholder' => 'To'])?>
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