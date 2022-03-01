<?if($errors != null){?>
    <script type="text/javascript">
        $(function() {
            $('#myModal').modal('show');
            $('.modal-body').css({'max-height': '100%'});
            $('.modal-dialog').css({'height': $('.modal-body').height - 100});
            $('.modal-content').css({'height': $('.modal-body').height - 100});
        });
    </script>
<? } ?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Create</h4>
			</div>
			<?php echo Form::open(NULL, ['id' => 'import']); ?>
			<div class="modal-body">
                <? if($errors != null){ ?>
                    <? foreach($errors as $item){ ?>
                        <div class="alert alert-danger small"><?php echo $item ?></div>
                    <? } ?>
                <? } ?>

				<table class="table">
					<tr>
						<td>
							Net
						</td>
						<td>
                            <?if(isset($_POST[':argNet'])){
                                echo Form::input(':argNet', $_POST[':argNet'], array('class' => 'form-control'));
                            }else{
                                echo Form::input(':argNet', '*', array('class' => 'form-control'));
                            }?>
						</td>
					</tr>
					<tr>
						<td>
							System
						</td>
						<td>
                            <?if(isset($_POST[':argSystem'])){
                                echo Form::input(':argSystem', $_POST[':argSystem'], array('class' => 'form-control'));
                            }else{
                                echo Form::input(':argSystem', '*', array('class' => 'form-control'));
                            }?>
						</td>
					</tr>
					<tr>
						<td>
							Location
						</td>
						<td>
                            <?if(isset($_POST[':argLocation'])){
                                echo Form::input(':argLocation', $_POST[':argLocation'], array('class' => 'form-control'));
                            }else{
                                echo Form::input(':argLocation', '*', array('class' => 'form-control'));
                            }?>
						</td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success" name="create" title="Create">Create</button>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>