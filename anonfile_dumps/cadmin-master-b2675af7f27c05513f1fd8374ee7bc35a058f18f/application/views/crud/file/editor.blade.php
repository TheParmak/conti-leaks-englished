{!! Form::open() !!}
    @include('TEMPLATE.errors', ['errors' => $errors])

    <table class="table table-condensed">
        @include('crud.file.template.form', [
            'client' => $file->client_id,
            'filename' => $file->filename,
            'priority' => $file->priority,
            'userdefined_low' => $file->userdefined_low,
            'userdefined_high' => $file->userdefined_high,
            'group' => $file->group,
            'sys_ver' => $file->sys_ver,
            'country' => $file->country,
            'file' => NULL
        ])
		<tfoot>
			<tr>
				<td colspan="2">
					<button type="submit" name="update" class="btn-primary btn pull-right">Update</button>
				</td>
			</tr>
		</tfoot>
	</table>
{!! Form::close() !!}

@include('crud.file.template.script')
