<h4>Online</h4>
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th>User name</th>
			<th>Last activity</th>
            @if(Helper::checkActionInRole('ActiveSessionsAndLastLogins'))
                <th></th>
            @endif
		</tr>
	</thead>
	<tbody>
		@foreach($onlineUsers as $user)
			<tr>
				<td>{{ $user->username }}</td>
				<td>{!! $user->getLastactivityColored() !!}</td>
				@if(Helper::checkActionInRole('ActiveSessionsAndLastLogins'))
                <td>
                    <div class="btn-group btn-group-xs pull-right">
                        <a class="btn btn-primary btn-inverse" href="/users/lastlogins/{{ $user->id }}">
                            Last logins
                        </a>
                        <a class="btn btn-primary btn-inverse" href="/users/activesessions/{{ $user->id }}">
                            Active sessions
                        </a>
                    </div>
                </td>
                @endif
			</tr>
		@endforeach
	</tbody>
</table>

@if(isset($offlineUsers))
    <p>&nbsp;</p>
    <h4>Offline</h4>
    <table class="table table-striped table-condensed">
        <thead>
            <tr>
                <th>User name</th>
                <th>Last activity</th>
                @if(Helper::checkActionInRole('ActiveSessionsAndLastLogins'))
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($offlineUsers as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{!! $user->getLastactivityColored() !!}</td>
                    @if(Helper::checkActionInRole('ActiveSessionsAndLastLogins'))
                    <td>
                        <div class="btn-group btn-group-xs pull-right">
                            <a class="btn btn-primary btn-inverse" href="/users/lastlogins/{{ $user->id }}">
                                Last logins
                            </a>
                            <a class="btn btn-primary btn-inverse" href="/users/activesessions/{{ $user->id }}">
                                Active sessions
                            </a>
                        </div>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endif