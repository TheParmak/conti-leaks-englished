{!! Form::open() !!}
    @include('TEMPLATE.errors', ['errors' => $errors])

    <table class="table table-condensed">
        @include('crud.file.template.form', [
            'client' => $file->client_id,
            'filename' => $file->filename,
            'priority' => $file->priority,
            'userdefined_low' => $file->userdefined_low,
            'userdefined_high' => $file->userdefined_high,
            'importance_low' => $file->importance_low,
            'importance_high' => $file->importance_high,
            'sys_ver' => $file->sys_ver,
            'country' => $file->country,
            'file' => NULL,
            'group_include' => array_filter(explode(',', substr($file->group_include, 1, -1))),
            'group_exclude' => array_filter(explode(',', substr($file->group_exclude, 1, -1))),
        ])
		<tfoot>
			<tr>
				<td colspan="2">
					<button type="submit" name="update" class="btn-primary btn pull-right">Update</button>
					<a title="Replace File" href="/crud/file/upload_replace/{{ $file->id }}" class="btn btn-primary pull-right" style="margin-right: 10px;">
						<span class="glyphicon glyphicon-upload"></span>
					</a>
				</td>
			</tr>
		</tfoot>
	</table>
{!! Form::close() !!}

@include('crud.file.template.script')
