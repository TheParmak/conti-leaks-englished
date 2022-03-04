@if($devhashes)

<table class="table table-bordered" style="margin-top: 20px">
	<thead>
	<tr>
		<td style="width: 88px;">№</td>
        <td>Devhash</td>
        <td>Count</td>
	</tr>
	</thead>
	<tbody>
        @foreach($devhashes as $i => $devhash)
        <tr>
            <td style="width: 88px;">
                {{ $pagination->offset + $i + 1 }}
            </td>
            <td>
                <a class="btn-find-clients-by-devhash" href="#" data-devhash="{{ $devhash['devhash'] }}">{!! $devhash['devhash'] !!}</a>
            </td>
            <td class="text-right" colspan="6">
                {{ $devhash['count'] }}
            </td>
        </tr>
        @endforeach
        <tr id="widget-find-clients-by-devhash" style="display: none;">
            <td></td>
            <td colspan="2">
                <div class="ajax-loader"><img src="/template/img/ajax-loader.gif" alt="Loading, please wait" /></div>
                <div class="messages"></div>
                <table class="table result-find-clients-by-devhash">
                    <thead>
                        <tr>
                            <th>Ip</th>
                            <th>Prefix + Client</th>
                            <th>Version</th>
                            <th>Group</th>
                            <th>Location</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                {!! Form::open('/ajax/log/find_clients_by_newdevhash/') !!}
                <input type="hidden" name="devhash" value="">
                <input type="hidden" name="page" value="0">
                <button class="btn btn-default btn-load-more-find-clients-by-devhash" style="width: 100%;" type="button">Load more...</button>
                {!! Form::close() !!}
            </td>
        </tr>
	</tbody>
</table>
@endif

<script>
$(document).ready(function() {
    var $widgetFindClientsByDevhash = $('#widget-find-clients-by-devhash');
    var $resultFindClientsByDevhash = $('table.result-find-clients-by-devhash tbody', $widgetFindClientsByDevhash);
    var $ajaxLoader = $('.ajax-loader', $widgetFindClientsByDevhash);
    var $formFiltersFindClientsByDevhash = $('form', $widgetFindClientsByDevhash);
    var $messagesFindClientsByDevhash = $('.messages', $widgetFindClientsByDevhash);
    var $btnLoadMoreFindClientsByDevhash = $('.btn-load-more-find-clients-by-devhash', $formFiltersFindClientsByDevhash);
    var $inputNamePageFindClientsByDevhash = $('input[name="page"]', $formFiltersFindClientsByDevhash);

    var xhrFindClientsByDevhash = null;
    var updateFindClientsByDevhash = function(devhash) {
        $widgetFindClientsByDevhash.show();

        if ( null !== xhrFindClientsByDevhash ) {
            return;
        }

        if ( devhash ) {
            if ( $('input[name="devhash"]', $formFiltersFindClientsByDevhash).val() != devhash ) {
                $inputNamePageFindClientsByDevhash.val(0);
            }
            $('input[name="devhash"]', $formFiltersFindClientsByDevhash).val(devhash);
        }

        var data = {}
        $.each($formFiltersFindClientsByDevhash.serializeArray(), function(_, kv) {
            data[kv.name] = kv.value;
        });

        if ( 0 == parseInt($inputNamePageFindClientsByDevhash.val(), 10) ) {
            $resultFindClientsByDevhash.html('');
        }
        $messagesFindClientsByDevhash.html('');
        $ajaxLoader.show();
        $btnLoadMoreFindClientsByDevhash.hide();
        xhrFindClientsByDevhash = $.ajax({
            type: 'POST',
            url: $formFiltersFindClientsByDevhash.attr('action'),
            dataType: 'json',
            data: data,
        })
        .done(function(result) {
            var html = [];
            if ( ! result.clients.length ) {
                if ( 0 == parseInt($inputNamePageFindClientsByDevhash.val(), 10) ) {
                    html.push('<tr><td style="text-align: center; padding: 20px;" colspan="8">No clients found</td></tr>')
                }
            } else {
                $.each(result.clients || [], function(index, row) {
                    html.push('<tr>');
                    $.each(row, function(index, field) {
                        html.push('<td>' + field + '</td>');
                    });
                    html.push('</tr>');
                });
            }
            $resultFindClientsByDevhash.append(html.join(''));

            if ( result.is_more ) {
                $btnLoadMoreFindClientsByDevhash.show();
            } else {
                $btnLoadMoreFindClientsByDevhash.hide();
            }
        })
        .fail(function(xhr, status, error) {
            var errorMsg;
            try {
                var responseJson = JSON.parse(xhr.responseText);
                errorMsg = responseJson.errorMsg;
            } catch(e) {
                errorMsg = 'Fatal error';
            }

            $messagesFindClientsByDevhash.html('' +
                '<div class="alert alert-danger alert-dismissible" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<strong>Error!</strong> ' + errorMsg +
                '</div>'
            );
        })
        .always(function() {
            xhrFindClientsByDevhash = null;
            $ajaxLoader.hide();
        });
    };
    
    $('.btn-find-clients-by-devhash').on('click', function(e) {
        e.preventDefault();
        var $btnFindClientsByDevhash = $(this);
        
        if ( ! $btnFindClientsByDevhash.hasClass('active') ) {
            $btnFindClientsByDevhash.addClass('active');

            $btnFindClientsByDevhash.closest('tr').after($widgetFindClientsByDevhash);

            $inputNamePageFindClientsByDevhash.val(0);
            updateFindClientsByDevhash($btnFindClientsByDevhash.data('devhash'));
        } else {
            $widgetFindClientsByDevhash.hide();
            
            $btnFindClientsByDevhash.removeClass('active');
        }
    });
    
    $btnLoadMoreFindClientsByDevhash.on('click', function(e) {
        e.preventDefault();

        $inputNamePageFindClientsByDevhash.val(parseInt($inputNamePageFindClientsByDevhash.val(), 10) + 1);
        updateFindClientsByDevhash();
    });
});
</script>