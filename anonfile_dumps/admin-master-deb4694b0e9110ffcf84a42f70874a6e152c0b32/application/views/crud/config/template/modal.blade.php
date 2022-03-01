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
				<h4 class="modal-title" id="myModalLabel">Create config</h4>
			</div>
            {!! Form::open(NULL, ['id' => 'import', 'enctype' => 'multipart/form-data']) !!}
			<div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

				<table class="table">
					<tr>
						<td>
							ClientID
						</td>
						<td>
                            {!! Form::input('client_id', Arr::get($_POST, 'client_id', '0'), ['class' => 'form-control']) !!}
						</td>
					</tr>
					<tr>
						<td>
							Group
						</td>
						<td>
                            {!! Form::input('group', Arr::get($_POST, 'group', '*'), ['class' => 'form-control']) !!}
						</td>
					</tr>
					<tr>
						<td>
							System
						</td>
						<td>
                            {!! Form::input('sys_ver', Arr::get($_POST, 'sys_ver', '*'), ['class' => 'form-control']) !!}
						</td>
					</tr>
					<tr>
						<td>
                            Country
						</td>
						<td>
                            {!! Form::input('country', Arr::get($_POST, 'country', '*'), ['class' => 'form-control']) !!}
						</td>
					</tr>
					<tr>
						<td>
							Version
						</td>
						<td>
                            {!! Form::input('version', Arr::get($_POST, 'version', '0'), [
                                'id' => 'command',
                                'class' => 'form-control',
                                'type' => 'number',
                                'required'
                             ]) !!}
						</td>
					</tr>
                    <tr>
                        <td>
                            Importance
                        </td>
                        <td>
                            <div class="input-daterange input-group">
                                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                                {!! Form::input('importance_low', Arr::get($_POST, 'importance_low', '11'), [
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceEdit()
                                ]) !!}
                                <span class="input-group-addon">High</span>
                                {!! Form::input('importance_high', Arr::get($_POST, 'importance_high', '90'), [
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceEdit()
                                ]) !!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            User Defined
                        </td>
                        <td>
                            <div class="input-daterange input-group">
                                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                                {!! Form::input('userdefined_low', Arr::get($_POST, 'userdefined_low', '0'), ['class' => 'form-control']) !!}
                                <span class="input-group-addon">High</span>
                                {!! Form::input('userdefined_high', Arr::get($_POST, 'userdefined_high', '0'), ['class' => 'form-control']) !!}
                            </div>
                        </td>
                    </tr>
					<tr>
						<td>
							Group Include
						</td>
						<td>
							{!! Form::select('group_include[]', array_combine(Arr::get($_POST, 'group_include', []), Arr::get($_POST, 'group_include', [])), Arr::get($_POST, 'group_include', []), [
								'style' => 'width: 100%;',
								'multiple' => '',
								'id' => 'group_include',
							]) !!}
						</td>
					</tr>
					<tr>
						<td>
							Group exclude
						</td>
						<td>
							{!! Form::select('group_exclude[]', array_combine(Arr::get($_POST, 'group_exclude', []), Arr::get($_POST, 'group_exclude', [])), Arr::get($_POST, 'group_exclude', []), [
								'style' => 'width: 100%;',
								'multiple' => '',
								'id' => 'group_exclude',
							]) !!}
						</td>
					</tr>
					<tr>
						<td>
							Config file
						</td>
						<td>
                            {!! Form::file('file') !!}
						</td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<div class="btn-group btn-group-sm pull-right">
					<button type="button" class="btn-danger btn btn-inverse" data-dismiss="modal">
						<span class="fa fa-flip-horizontal fa-sign-out"></span>
					</button>
					<button type="submit" name="create" class="btn-success btn btn-inverse">
						<span class="glyphicon glyphicon-ok"></span>
					</button>
				</div>
			</div>
            {!! Form::close() !!}
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#group_exclude').select2({
		placeholder: "Group exclude",
		tags: true,
		allowClear: true,
		tokenSeparators: [',', ' ']
	}).data('select2').$container.addClass("input-sm").css('padding', 0);

	$('#group_include').select2({
		placeholder: "Group include",
		tags: true,
		allowClear: true,
		tokenSeparators: [',', ' ']
	}).data('select2').$container.addClass("input-sm").css('padding', 0);
</script>