<?=Form::open()?>
<table class="table">
    <thead>
        <tr>
            <td style="width: 10px">
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>Name</td>
            <td>Password</td>
            <td>From</td>
            <td>To</td>
            <td>Proc</td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        <?foreach($users as $user){ ?>
        <tr>
            <td>
                <input type="checkbox" name="check[]" value="<?php echo $user[0] ?>">
            </td>
            <td><?=$user[0]?></td>
            <td><?=$user[1]?></td>
            <td>
                <?if(isset($user[2])){?>
                    <?=$user[2]?>
                <?}?>
            </td>
            <td>
                <?if(isset($user[3])){?>
                    <?=$user[3]?>
                <?}?>
            </td>
            <td>
                <?if(isset($user[4])){?>
                    <ul>
                        <? foreach($user[4] as $u){?>
                            <li>
                                <?php echo $u?>
                            </li>
                        <?}?>
                    </ul>
                <?}?>
            </td>
            <td>
                <a class="btn btn-primary pull-right" href="/remoteusers/add_remote_user_ip/<?=$user[0]?>">
                    AddRemoteUserIP
                </a>
                <a class="btn btn-primary pull-right" href="/remoteusers/add_proc/<?=$user[0]?>" style="margin-right: 10px;">
                    AddRemoteUserProc
                </a>
            </td>
        </tr>
        <?}?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
                <a class="btn btn-primary pull-right" href="/remoteusers/editor/">AddRemoteUser</a>
                <?=Form::button('delete', 'DeleteRemoteUser', ['class' => 'btn btn-danger pull-right', 'style' => 'margin-right:10px;']);?>
            </td>
        </tr>
    </tfoot>
</table>
<?=Form::close()?>

<?=View::factory('remote_users/v_script');?>