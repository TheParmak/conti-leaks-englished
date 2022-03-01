{!! Form::open() !!}
    @include('TEMPLATE.errors', ['errors' => $errors])

    <table class="table table-condensed">
        @include('crud.config.template.form', [
            'client_id' => $model->client_id,
            'group' => $model->group,
            'sys_ver' => $model->sys_ver,
            'country' => $model->country,
            'version' => $model->version,
            'userdefined_low' => $model->userdefined_low,
            'userdefined_high' => $model->userdefined_high,
            'importance_low' => $model->importance_low,
            'importance_high' => $model->importance_high,
            'file' => NULL,
            'group_include' => array_filter(explode(',', substr($model->group_include, 1, -1))),
            'group_exclude' => array_filter(explode(',', substr($model->group_exclude, 1, -1))),
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

@include('crud.config.template.script')
