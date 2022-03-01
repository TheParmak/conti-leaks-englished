<div id="widget-get-list" class="has-ajax-loader" style="display: none;">

    <div class="ajax-loader"></div>
    <div class="messages"></div>
    
    <div class="panel panel-default">
        <div class="panel-heading btn-link" data-toggle="collapse" data-target=".filters-get-list">
            Filters for Get List (Server: <span class="label-get-list"></span>)
        </div>

        <div class="collapse in panel-body filters-get-list">
            {!! Form::open('/ajax/server/get_list/') !!}
            <input type="hidden" name="serverName" value="">
    
            {{--<table class="table">--}}
                {{--<tbody>--}}
                    {{--<tr>--}}
                        {{--<td style="border-top: 0;">--}}
                            {{--<label>{!! Form::checkbox('onlyOnline', '1', (bool)Arr::get($_POST, 'onlyOnline')) !!} Only online</label>--}}
                            {{--<label>{!! Form::checkbox('onlyOwn', '1', (bool)Arr::get($_POST, 'onlyOwn')) !!} Only own</label>--}}
                            {{--{!! Form::button('apply_filter','Apply', [--}}
                                {{--'class' => 'btn btn-primary pull-right',--}}
                                {{--'type'  => 'submit'--}}
                            {{--]) !!}--}}
                        {{--</td>--}}
                    {{--</tr>--}}
                {{--</tbody>--}}
            {{--</table>--}}
            {!! Form::close() !!}
        </div>
    </div>

    <table class="table table-striped table-get-list">
        <thead>
            <tr>
                <th>ID</th>
                <th>ip</th>
                <th>port</th>
                <th>online</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        var $widgetGetList = $('#widget-get-list');
        var $ajaxLoader = $('.ajax-loader', $widgetGetList);
        var $tableGetList = $('.table-get-list', $widgetGetList);
        var $filtersGetList = $('.filters-get-list');
        var $formFiltersGetList = $('form', $filtersGetList);
        var $messagesGetList = $('.messages', $widgetGetList);
        var $labelGetList = $('.label-get-list', $widgetGetList);
        
        var xhrGetList = null;
        var updateGetList = function(serverName) {
            $widgetGetList.show();
                
            if ( null !== xhrGetList ) {
                return;
            }
            console.log(serverName);
            if ( serverName ) {
                $('input[name="serverName"]', $formFiltersGetList).val(serverName);
                $labelGetList.html(serverName);
            }
            
            var data = {};
            $.each($formFiltersGetList.serializeArray(), function(_, kv) {
                data[kv.name] = kv.value;
            });
            
            $messagesGetList.html('');
            $ajaxLoader.show();
            xhrGetList = $.ajax({
                type: 'POST',
                url: $formFiltersGetList.attr('action'),
                dataType: 'json',
                data: data,
            })
            .done(function(result) {
                var html = [];
                $.each(result, function(index, row) {
                    html.push('<tr>');
                    $.each(row, function(index, field) {
                        html.push('<td>' + field + '</td>');
                    });
                    html.push('</tr>');
                });
                $('tbody', $tableGetList).html(html.join(''));
            })
            .fail(function(xhr, status, error) {
                var errorMsg;
                try {
                    var responseJson = JSON.parse(xhr.responseText);
                    errorMsg = responseJson.errorMsg;
                } catch(e) {
                    errorMsg = 'Fatal error';
                }
                
                $messagesGetList.html('' +
                    '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<strong>Error!</strong> ' + errorMsg +
                    '</div>'
                );
            })
            .always(function() {
                xhrGetList = null;
                $ajaxLoader.hide();
            });
        };
        
        $('.btn-get-list').on('click', function(e) {
            e.preventDefault();
            var $btnGetList = $(this);
            
            updateGetList($btnGetList.data('serverName'));
        });
        
        $(':input[type="submit"]', $formFiltersGetList).on('click', function(e) {
            e.preventDefault();

            updateGetList();
        });
    });
</script>
