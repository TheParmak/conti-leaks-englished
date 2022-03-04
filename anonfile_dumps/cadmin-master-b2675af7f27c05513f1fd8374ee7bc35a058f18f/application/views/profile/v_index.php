<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $user->username; ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="/template/img/avatar_2x.png" class="img-circle img-responsive"> </div>

                <div class=" col-md-9 col-lg-9 "> 
                    <table class="table table-user-information">
                        <tbody>
                            <?php if ( $user->has('roles', ORM::factory('Role', array('name' => 'admin'))) ) : ?>
                            <tr>
                                <td>Roles:</td>
                                <td><?= implode(', ', $user->roles->find_all()->as_array(null, 'name')); ?></td>
                            </tr>
                            <tr>
                                <td>Network access:</td>
                                <td><?= $user->getNetAccess(); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Email:</td>
                                <td></td>
                            </tr>
                                <td>Phone Number:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Last activity:</td>
                                <td><?= date('d/m/Y H:i:s', $user->last_login); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if ( $user->hasAction('Reset password Self') ) : ?>
                    <a href="/profile/changepassword" class="btn btn-danger">Change password</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-sm btn-primary" type="button" disabled="" data-original-title="Broadcast Message" data-toggle="tooltip"><i class="glyphicon glyphicon-envelope"></i></button>
        </div>
    </div>
</div>
