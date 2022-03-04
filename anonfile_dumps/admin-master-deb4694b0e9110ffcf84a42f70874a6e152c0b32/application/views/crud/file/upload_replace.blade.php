{!! Form::open(null, ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title text-center">FileReplace <b>{{ $file->filename }}</b></h4>
		</div>
		<div class="modal-body">
			<div class="form-group form-group-sm">
				<label for="sys_ver" class="col-sm-2 control-label">File</label>
				<div class="col-sm-10">
					{!! Form::file('file') !!}
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="btn-group btn-group-sm pull-right">
				<a href="/crud/file/" class="btn-danger btn btn-inverse">
					<span class="fa fa-flip-horizontal fa-sign-out"></span>
				</a>
				<button type="submit" name="apply" class="btn-success btn btn-inverse">
					<span class="glyphicon glyphicon-ok"></span>
				</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}