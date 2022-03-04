<?php if (isset($dataaccount)) : ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Data Account <span class="pull-right"><?= $dataaccount->datetime; ?></span></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <pre style="overflow-y: auto; max-height: 400px;"><?= $dataaccount->data; ?></pre>
            </td>
        </tr>
    </tbody>
</table>
<?php endif; ?>
