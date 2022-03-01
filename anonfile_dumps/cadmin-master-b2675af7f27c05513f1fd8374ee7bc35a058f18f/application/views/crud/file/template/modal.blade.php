@if($errors)
    <script type="text/javascript">
        $(function() {
            $('#myModal').modal('show');
            $('.modal-body').css({'max-height': '100%'});
            $('.modal-dialog').css({'height': $('.modal-body').height - 100});
            $('.modal-content').css({'height': $('.modal-body').height - 100});
        });
    </script>
@endif

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Upload</h4>
			</div>
            {!! Form::open(NULL, ['id' => 'upload', 'enctype' => 'multipart/form-data']) !!}
			<div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

                <table class="table table-condensed">
                    @include('crud.file.template.form', [
                        'client' => Arr::get($_POST, 'client', '0'),
                        'filename' => Arr::get($_POST, 'filename'),
                        'priority' => Arr::get($_POST, 'priority'),
                        'userdefined_low' => Arr::get($_POST, 'userdefined_low', '0'),
                        'userdefined_high' => Arr::get($_POST, 'userdefined_high', '0'),
                        'group' => Arr::get($_POST, 'group', '*'),
                        'sys_ver' => Arr::get($_POST, 'sys_ver', '*'),
                        'country' => Arr::get($_POST, 'country', '*'),
                        'file' => Form::file('file')
                    ])
                </table>
			</div>
			<div class="modal-footer">
				{!! Form::button('send', 'Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
				{!! Form::button('upload', 'Upload', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>