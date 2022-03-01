@if(count($clients))
    Total {{ $clients[0]['total_count'] }}
@endif

<table class="table table-bordered table-striped table-condensed">
	<thead>
		<tr>
			<td>Location</td>
			<td>Count</td>
		</tr>
	</thead>
	<tbody>
		@foreach ($clients as $client)
			<tr>
				<td>
                    @if(isset($client['country']))
                        {{ $client['country'] }}
                    @endif
                </td>
				<td>
                    {{ $client['cnt'] }}
                </td>
			</tr>
		@endforeach
	</tbody>
</table>