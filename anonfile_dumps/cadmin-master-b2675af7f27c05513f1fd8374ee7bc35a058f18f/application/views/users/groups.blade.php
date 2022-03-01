<noscript>
{!! Form::open() !!}
    <table class="table">
        <thead>
            <tr>
                <td>
                    <span class="glyphicon glyphicon-check"></span>
                </td>
                <td>Net</td>
            </tr>
        </thead>
        <tbody class="small">
            @foreach($nets as $net)
                <tr>
                    <td style="width: 10px">
                        @if($file != null)
                            {!! Form::checkbox('nets[]', $net, in_array($net, $file), ['class' => 'pull-left']) !!}
                        @else
                            {!! Form::checkbox('nets[]', $net, false, ['class' => 'pull-left']) !!}
                        @endif
                    </td>
                    <td>
                        <?php echo $net?>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    {!! Form::button('update', 'Update', ['class' => 'btn btn-success']) !!}
                </td>
            </tr>
        </tfoot>
    </table>
{!! Form::close() !!}
</noscript>

<div id="net_access_js" class="col-md-12" style="display: none;">
    {!! Form::open() !!}
        <div class="form-group">
            <label style="font-weight: normal;">Allowed nets for user <strong>{{ $user->username }}</strong> <span class="glyphicon glyphicon-question-sign text-primary" title="By default user has access to all nets. Restrict access by selecting them in input box." data-toggle="tooltip" data-placement="right"></span></label>
            <div class="clearfix">
                <select style="width: 100%; width: calc(100% - 30px);" name="nets[]" multiple="">
                    @foreach($nets as $net)
                        <option value="{{ $net }}"{{ in_array($net, (array)$file) ? 'selected' : '' }}>{{ $net }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="checkbox" style="padding-left: 20px; font-weight: normal;">
                <input class="checkbox-allow-all" type="checkbox">
                Allow access to all
            </label>
        </div>
        {!! Form::button('update', 'Update', ['class' => 'btn btn-success']) !!}
    {!! Form::close() !!}
</div>

<script>
$(document).ready(function() {
    var $scopeWidget = $('#net_access_js');
    var $selectNets = $('select[name="nets[]"]', $scopeWidget);
    var $checkboxAllowAll = $('.checkbox-allow-all', $scopeWidget);
    
    $scopeWidget.show();
    $selectNets.select2({
        placeholder: 'Allow access to all'
    });
    $('[data-toggle="tooltip"]', $scopeWidget).tooltip();
    
    var clickedCheckboxAllowAll = false;
    $selectNets.on('change', function() {
        if ( ! clickedCheckboxAllowAll ) {
            $checkboxAllowAll.prop('checked', false);
        }
        clickedCheckboxAllowAll = false;
        
        if ( ! $(':selected', $(this)).length ) {
            $checkboxAllowAll.prop('checked', true);
        }
    });
    $checkboxAllowAll.on('click', function() {
        if ( $(this).prop('checked') ) {
            clickedCheckboxAllowAll = true;
            $selectNets
                .children('option')
                .prop('selected', false)
                .end()
                .trigger('change');
        }
    });
});
</script>
    