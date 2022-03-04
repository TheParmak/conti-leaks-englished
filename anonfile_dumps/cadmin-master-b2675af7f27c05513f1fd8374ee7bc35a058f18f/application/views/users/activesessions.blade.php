<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <th>Type</th>
            <th>Ip</th>
            <th>User agent</th>
            <th>Last active</th>
        </tr>
    </thead>
    <tbody class="small">
        @foreach($activesessions as $activesession)
            <tr>
                @if($activesession instanceof Model_Session)
                    <td>
                        Session
                    </td>
                    <td>
                        {{ $activesession->ip }}
                    </td>
                    <td>
                        {{ $activesession->user_agent }}
                    </td>
                    <td>
                        {!! $activesession->getLastActiveColored() !!}
                    </td>
                @else
                    <td>
                        &quot;Remember me&quot; token
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        {!! $activesession->getCreatedColored() !!}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
