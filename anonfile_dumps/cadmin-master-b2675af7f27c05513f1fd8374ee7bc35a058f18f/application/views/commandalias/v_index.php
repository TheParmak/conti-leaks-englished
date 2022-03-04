<?= Form::open(); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <td>
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>
                Alias
            </td>
            <td>
                Command
            </td>
            <td>
                Param
            </td>
            <td>
                Login
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($commandaliases as $item){ ?>
            <tr>
                <td>
                    <input class="argID" type="checkbox" name="check[][:argID]" value="<?= $item['id']; ?>">
                </td>
                <td>
                    <?= $item['alias']; ?>
                </td>
                <td>
                    <?= $item['command']; ?>
                </td>
                <td>
                    <?= $item['param']; ?>
                </td>
                <td>
                    <?= $item['name']; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="13">
            <input type="checkbox" id="select_all">
            <button name="push_back" type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal">
                Add Command Alias
            </button>
            <button type="submit" name="deleteCommandAliases" title="Delete" class="btn btn-danger pull-right" style="margin-right: 10px;">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
    </tr>
    </tfoot>
</table>
<?= Form::close(); ?>

<?php if ($errors != null) : ?>
    <script type="text/javascript">
        $(function() {
            $('#myModal').modal('show');
            $('.modal-body').css({'max-height': '100%'});
            $('.modal-dialog').css({'height': $('.modal-body').height - 100});
            $('.modal-content').css({'height': $('.modal-body').height - 100});
        });
    </script>
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Command Alias</h4>
            </div>
            <?= Form::open(); ?>
            <div class="modal-body">
                <?php if($errors != null){ ?>
                    <?php foreach($errors as $item){ ?>
                        <div class="alert alert-danger small"><?= $item ?></div>
                    <?php } ?>
                <?php } ?>

                <table class="table">
                    <tr>
                        <td>
                            Alias
                        </td>
                        <td>
                            <?= Form::input(':argAlias', Arr::get($_POST, ':argAlias'), ['class' => 'form-control']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Command
                        </td>
                        <td>
                            <?= Form::input(':argCommand', Arr::get($_POST, ':argCommand'), ['class' => 'form-control', 'type' => 'number']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Param
                        </td>
                        <td>
                            <?= Form::input(':argParam', Arr::get($_POST, ':argParam'), ['class' => 'form-control']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Name
                        </td>
                        <td>
                            <?= Form::input(':argName', Arr::get($_POST, ':argName'), ['class' => 'form-control']); ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" name="create" title="Create">Create</button>
            </div>
            <?= Form::close(); ?>
        </div>
    </div>
</div>

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
        
        var $scopeModal = $('#myModal');
        
        // no interactive elements in this modal
        
    });
</script>