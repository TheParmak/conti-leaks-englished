<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <td>
                CreatedAt
            </td>
            <td>
                Client
            </td>
            <td>
                Group
            </td>
            <td>
                Country
            </td>
            <td>
                Version
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($configs as $config)
            <tr>
                <td>
                    {{ $config->created_at }}
                </td>
                <td>
                    {!! $config->client->getLink() !!}
                </td>
                <td>
                    {{ $config->group }}
                </td>
                <td>
                    {{ $config->country }}
                </td>
                <td>
                    {{ $config->version }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>