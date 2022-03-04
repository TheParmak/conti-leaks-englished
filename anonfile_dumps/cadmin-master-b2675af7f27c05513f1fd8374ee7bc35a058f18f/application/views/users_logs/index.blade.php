<div class="col-md-12 well">
	<div class="row-fluid">
        {!! Form::open(null, ['id' => 'widget-filter']) !!}
            <!-- BTN -->
            <div class="btn-group btn-group-sm pull-right">
                {!! Form::button('find', 'Find', [
                    'class'=>'btn btn-primary btn-inverse',
                    'type'=>'submit'
                ]) !!}
                {!! Form::button('reset', 'Reset', [
                    'class'=>'btn btn-danger btn-inverse',
                    'type'=>'submit',
                ]) !!}
            </div>

			<div id="find_users_js" class="col-xs-2">
				{!! Form::select('find_users[]', $users, Arr::get($post, 'find_users'), [
					'class' => 'selectpicker',
					'multiple' => '',
					'style' => 'width: 100%; width: calc(100% - 30px);'
				]) !!}
			</div>

			<!-- Data range -->
			<div class="col-xs-2 input-daterange input-group datepicker-range">
				{!! Form::input('from', Arr::get($post, 'from'), [
					'id' => 'from',
					'class' => 'form-control input-sm',
					'placeholder' => '1901/12/13'
				]) !!}

				<span class="input-group-addon"> / </span>

				{!! Form::input('to', Arr::get($post, 'to'), [
					'id' => 'to',
					'class' => 'form-control input-sm',
					'placeholder' => '2038/01/19'
				]) !!}
			</div>
		{!! Form::close() !!}
	</div>
</div>

<div class="col-md-12">
	<div class="row-fluid">
		{!! $pagination !!}
		<table class="table table-striped table-condensed">
			<thead>
			<tr>
				<td style="width: 10px;">User</td>
				<td>Data</td>
				<td style="text-align: right">Timestamp</td>
			</tr>
			</thead>
			<tbody style="word-break: break-all;">
            @foreach($logs as $log)
                <tr>
					<td>
						{{ $log->user }}
					</td>
					<td>{!! $log->data !!}</td>
					<td style="text-align: right">
						{!! date("Y-m-d H:i:s", strtotime($log->timestamp)+7200) !!}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		{!! $pagination !!}
	</div>
</div>

<script>
    $(document).ready(function() {
	    var $scopeWidget = $('#widget-filter');
	    var $selectUsers = $('select[name="find_users[]"]', $scopeWidget);
	    $selectUsers.select2({
		    placeholder: "Input user name.."
	    }).data('select2').$container.addClass("input-sm").css('padding', 0);

	    $('.datepicker-range', $scopeWidget).datepicker({
		    language: "en",
		    autoclose: true,
		    todayHighlight: true,
		    format: "yyyy/mm/dd"
	    });
    });
</script>