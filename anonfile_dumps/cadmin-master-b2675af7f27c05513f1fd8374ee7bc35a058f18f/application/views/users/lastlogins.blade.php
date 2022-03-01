<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Type</th>
            <th>Ip</th>
            <th>User agent</th>
            <th>Logged at</th>
        </tr>
    </thead>
    <tbody class="small">
        @foreach($user_lastlogins as $user_lastlogin)
            <tr>
                <td>
                    {{ $user_lastlogin->is_restored_from_rememberme ? '&quot;Remember me&quot; auto login' : 'Login' }}
                </td>
                <td>
                    {{ $user_lastlogin->ip }}
                </td>
                <td>
                    {{ $user_lastlogin->user_agent }}
                </td>
                <td>
                    {!! $user_lastlogin->getLoggedAtColored() !!}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
