<div id="widget-remove-process" class="container" data-count-total="<?= $countTotal; ?>">
    <div class="header">
        <h3 style="margin-top: 0;">Processing <?= $function; ?></h3>
        <p>Last activity from: <?= $from_lastactivity; ?></p>
        <p>Last activity to: <?= $to_lastactivity; ?></p>
        <p>Count processed: <span class="count-processed">0</span></p>
        <p>Count left: <span class="count-left"><?= $countTotal; ?></span></p>
        <p>Count total: <?= $countTotal; ?></p>
    </div>

    <div class="progress">
        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;">
            0%
        </div>
    </div>

    <?= Form::open('/remove/process_client'); ?>
        <input type="hidden" name="function" value="<?= $function; ?>" />
        <input type="hidden" name="from_lastactivity" value="<?= $from_lastactivity; ?>" />
        <input type="hidden" name="to_lastactivity" value="<?= $to_lastactivity; ?>" />
        <input type="hidden" name="i" value="0" />
        <button type="button" class="btn btn-danger btn-play-pause" data-state="play"><i class="glyphicon glyphicon-pause"></i></button>
        <a class="btn btn-primary btn-return pull-right" style="display: none;" href="/remove">Return</a>
    <?= Form::close(); ?>
        
    <div class="messages"></div>
</div>

<script>
    $(document).ready(function() {
        var $widgetRemoveProcess = $('#widget-remove-process');
        var $countProcessed = $('.count-processed', $widgetRemoveProcess);
        var $countLeft = $('.count-left', $widgetRemoveProcess);
        var $progressBar = $('.progress .progress-bar', $widgetRemoveProcess);
        var $form = $('form', $widgetRemoveProcess);
        var $btnPlayPause = $('.btn-play-pause', $form);
        var $btnReturn = $('.btn-return', $form);
        var $messages = $('.messages', $widgetRemoveProcess);
        var countTotal = $widgetRemoveProcess.data('countTotal');

        var updateProgress = function(i) {
            $countProcessed.html(i + '');
            $countLeft.html((countTotal - i) + '');
            
            var progressPercent = Math.round(i / countTotal * 100);
            $progressBar.css('width', progressPercent + '%').attr('aria-valuenow', progressPercent).html(progressPercent + '%');
        }

        var xhrRemoveProcessClient = null;
        var processNext = function(i) {
            if ( null !== xhrRemoveProcessClient ) {
                return;
            }
            
            if ( 'pause' == $btnPlayPause.data('state') ) {
                return;
            }

            if ( i ) {
                $('input[name="i"]', $form).val(i);
            }

            var data = {};
            $.each($form.serializeArray(), function(_, kv) {
                data[kv.name] = kv.value;
            });

            xhrRemoveProcessClient = $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                dataType: 'json',
                data: data,
            })
            .done(function(result) {
                if ( 'ok' == result.status ) {
                    i = i + 1;
                    setTimeout(function() {
                        processNext(i);
                    }, 0);
                } else if ( 'complete' == result.status ) {
                    i = countTotal;
                    $btnPlayPause.hide();
                    $btnReturn.show();
                } else {
                    // Not implemented
                    return;
                }
                
                updateProgress(i);
            })
            .fail(function(xhr, status, error) {
                var errorMsg;
                try {
                    var responseJson = JSON.parse(xhr.responseText);
                    errorMsg = responseJson.errorMsg;
                } catch(e) {
                    errorMsg = 'Fatal error';
                }
                
                $messages.append('' +
                    '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<strong>Error!</strong> ' + errorMsg +
                    '</div>'
                );
        
                setPauseBtnPlayPause();
            })
            .always(function() {
                xhrRemoveProcessClient = null;
            });
        };
        processNext(0);

        var setPlayBtnPlayPause = function() {
            $('.glyphicon', $btnPlayPause).removeClass('glyphicon-play').addClass('glyphicon-pause');
            $btnPlayPause.data('state', 'play');
            processNext();
        };
        var setPauseBtnPlayPause = function() {
            $('.glyphicon', $btnPlayPause).removeClass('glyphicon-pause').addClass('glyphicon-play');
            $btnPlayPause.data('state', 'pause');
        };
        var toggleBtnPlayPause = function() {
            if ( 'pause' == $btnPlayPause.data('state') ) {
                setPlayBtnPlayPause();
            } else {
                setPauseBtnPlayPause();
            }
        };
        $btnPlayPause.on('click', function(e) {
            e.preventDefault();
            
            toggleBtnPlayPause();
        });
    });
</script>