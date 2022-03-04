<?= Form::open(); ?>
    <?php if ( isset($success) ) : ?>
        <div class="alert alert-success small"><?= $success; ?></div>
    <?php endif; ?>
    <?php if ( isset($errors) ) : ?>
        <?php foreach($errors as $item) : ?>
            <div class="alert alert-danger small"><?= $item; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <table class="table">
        <tbody>
            <tr>
                <td>
                    <label>Last activity</label>
                </td>
                <td>
                    <div class="input-daterange input-group" id="datepicker">
                        <?= Form::input('from_lastactivity', Arr::get($post, 'from_lastactivity'), array('class' => 'form-control input-sm')); ?>
                        <span class="input-group-addon"> до </span>
                        <?= Form::input('to_lastactivity', Arr::get($post, 'to_lastactivity', date('Y/m/d', strtotime('-3 month'))), array('class' => 'form-control input-sm')); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label>ClientID</label>
                </td>
                <td>
                    <?= Form::input('clientid', Arr::get($post, 'clientid'), ['class' => 'form-control', 'placeholder' => 'ClientID or empty']); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" class="btn btn-danger pull-right" name="DeleteVars">DeleteVars</button>
                    <button type="submit" class="btn btn-danger pull-right" name="DeleteLog" style="margin-right: 10px;">DeleteLog</button>
                    <button type="submit" class="btn btn-danger pull-right" name="DeleteBackConnData" style="margin-right: 10px;">DeleteBackConnData</button>
                </td>
            </tr>
        </tbody>
    </table>
<?= Form::close(); ?>

<script>
    $(document).ready(function() {
        $('#datepicker').datepicker({
            language: "ru",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd"
        });
    });
</script>