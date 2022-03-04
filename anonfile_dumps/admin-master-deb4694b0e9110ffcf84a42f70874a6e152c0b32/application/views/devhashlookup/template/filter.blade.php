<div class="well well-sm">
	<div id="filter" class="panel-body">
		{!! Form::open() !!}
			<table class="table" id="clientFilterTable" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<td>Exact devhash</td>
						<td>Group</td>
						<td>CreatedAt</td>
						<td>LoggedAt</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<!-- Devhash -->
						<td>
                            {!! Form::input('devhash', Arr::get($post, 'devhash'), ['class' => 'form-control input-sm']) !!}
						</td>
						<!-- Net -->
						<td>
                            {!! Form::input('group', Arr::get($post, 'group'), ['class' => 'form-control input-sm']) !!}
						</td>
						<!-- Registration -->
						<td>
							<div class="input-daterange input-group bootstrap-datepicker">
                                {!! Form::input('registered_start', Arr::get($post, 'registered_start'), ['class' => 'form-control input-sm']) !!}
								<span class="input-group-addon">/</span>
                                {!! Form::input('registered_end', Arr::get($post, 'registered_end'), ['class' => 'form-control input-sm']) !!}
							</div>
						</td>
						<!-- Last Activity -->
						<td>
                            {!! Form::select('logged_at', $lastactivity_options, Arr::get($post, 'logged_at'), ['class' => 'selectpicker']) !!}
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">
                            {!! Form::button('apply_filter','Apply', [
                                'class' => 'btn btn-primary pull-right',
                                'type'  => 'submit'
                            ]) !!}
                            {!! Form::button('reset_filter','Reset', [
                                'style' => 'margin-right: 10px;',
                                'class' => 'btn btn-danger pull-right',
                                'type'  => 'submit'
                            ]) !!}
						</td>
					</tr>
				</tfoot>
			</table>
		{!! Form::close() !!}

        @if($pagination)
            <div class="row" style="text-align: right;">
                <div class="col-md-12">
                    Total devhashes: {{ $pagination->total_items }}
                </div>
            </div>
        @endif
	</div>
</div>

@include('devhashlookup/template/script')