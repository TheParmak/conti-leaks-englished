<?php echo Form::open(); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <td>
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>
                Net
            </td>
            <td>
                System
            </td>
            <td>
                Location
            </td>
        </tr>
        </thead>
        <tbody>
        <?php foreach($model as $item){ ?>
            <tr>
                <td>
                    <input type="checkbox" name="check[]" value="<?php echo $item[0] ?>">
                </td>
                <td>
                    <?php echo $item[1] ?>
                </td>
                <td>
                    <?php echo $item[2] ?>
                </td>
                <td>
                    <?php echo $item[3] ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6">
                <input type="checkbox" id="select_all">
                <button name="push_back" type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">
                    <span class="glyphicon glyphicon-new-window"></span>
                </button>
                <?=Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
                    'type' => 'submit',
                    'title' => 'Delete',
                    'class' => 'btn btn-danger pull-right',
                    'style' => 'margin-right: 10px;'
                ])?>
            </td>
        </tr>
        </tfoot>
    </table>
<?php echo Form::close(); ?>