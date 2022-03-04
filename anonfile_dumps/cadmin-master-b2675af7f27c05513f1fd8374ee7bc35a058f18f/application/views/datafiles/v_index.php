<table class="table table-bordered">
    <thead>
        <tr>
            <td>
                <b>№</b>
            </td>
            <td>
                <b>Name</b>
            </td>
            <td>
                <b>Client</b>
            </td>
            <td>
                <b>Datetime</b>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($datafiles as $key => $item) {
            ?>
            <tr>
                <td class="col-md-1">
                    <?= $pagination->getTotalItems() - ($pagination->items_per_page * ($pagination->getCurrentPage() - 1) + $key) ?>
                </td>
                <td>
                    <a href="/download/datafiles/<?php echo $item->sha1 ?>">
                        <?php echo $item->name ?>
                    </a>
                </td>
                <td>
                    <a href="/log/<?php echo $item->clientid ?>">
                        <?php echo ORM::factory('Client', $item->clientid)->getClientID() ?>
                    </a>
                </td>
                <td>
                    <?php echo $item->datetime ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php
echo $pagination->render();
?>