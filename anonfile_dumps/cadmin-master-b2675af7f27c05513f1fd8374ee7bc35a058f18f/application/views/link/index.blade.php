{!! Form::open() !!}
<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <td>
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>
                Datetime
            </td>
            <td>
                Client
            </td>
            <td>
                Group
            </td>
            <td>
                System
            </td>
            <td>
                Location
            </td>
            <td>UserDefinedLow</td>
            <td>UserDefinedHigh</td>
            <td>
                Expiration
            </td>
            <td>
                Link
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($links as $link)
            <tr>
                <td>
                    <input class="argID" type="checkbox" name="check[]" value="{{ $link->id }}">
                </td>
                <td>{!! $link->created_at !!}</td>
                <td>{!! $link->client->getLink() !!}</td>
                <td>{{ $link->group }}</td>
                <td>{{ $link->sys_ver }}</td>
                <td>{{ $link->country }}</td>
                <td>{{ $link->userdefined_low }}</td>
                <td>{{ $link->userdefined_high }}</td>
                <td>{{ $link->expiry_at }}</td>
                <td>{{ $link->url }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="13">
            <input type="checkbox" id="select_all">
            <button name="push_back" type="button" class="btn btn-primary pull-right btn-inverse" data-toggle="modal" data-target="#myModal">
                Add Link
            </button>
            <button type="submit" name="deleteLinks" title="Delete" class="btn btn-danger pull-right btn-inverse" style="margin-right: 10px;">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
    </tr>
    </tfoot>
</table>
{!! Form::close() !!}

@if($errors)
    <script type="text/javascript">
        $(function() {
            $('#myModal').modal('show');
            $('.modal-body').css({'max-height': '100%'});
            $('.modal-dialog').css({'height': $('.modal-body').height - 100});
            $('.modal-content').css({'height': $('.modal-body').height - 100});
        });
    </script>
@endif

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Link</h4>
            </div>
            <div class="modal-body">
                @if($errors)
                    @foreach($errors as $item)
                        <div class="alert alert-danger small">{!! $item  !!}</div>
                    @endforeach
                @endif

                {!! Form::open(null, ['class' => 'form-horizontal', 'id' => 'addLink']) !!}
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">ClientID</label>
                        <div class="col-sm-10">
                            {!! Form::input(':argClientID', Arr::get($_POST, ':argClientID', '0'), [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">Group</label>
                        <div class="col-sm-10">
                            {!! Form::input(':argNet', Arr::get($_POST, ':argNet', '*'), [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">System</label>
                        <div class="col-sm-10">
                            {!! Form::input(':argSystem', Arr::get($_POST, ':argSystem', '*'), [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">Location <span class="glyphicon glyphicon-question-sign text-primary" title="You can specify multiple locations by separating them using space symbol, for example, &quot;DE FR IT ES&quot;." data-toggle="tooltip" data-placement="right"></span></label>
                        <div class="col-sm-10">
                            {!! Form::input(':argLocation', Arr::get($_POST, ':argLocation', '*'), [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">UserDefined</label>
                        <div class="col-sm-10">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                                {!! Form::input(':userdefined_low', Arr::get($_POST, ':userdefined_low', 0), [
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'min' => 0,
                                ]) !!}
                                <span class="input-group-addon">High</span>
                                {!! Form::input(':userdefined_high', Arr::get($_POST, ':userdefined_high', 100), [
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'min' => 0,
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">Expiration</label>
                        <div class="col-sm-10">
                            {!! Form::hidden(':argExpiration', Arr::get($_POST, ':argExpiration')) !!}
                            <div class="input-daterange input-group">
                                <input id="expiration-hours" class="form-control" style="width: 85px;" type="number" min="0" />
                                <span class="input-group-addon">h</span>
                                <input id="expiration-minutes" class="form-control" type="number" min="0" max="59" />
                                <span class="input-group-addon" style="border-right-width: 1px;">m</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">Link</label>
                        <div class="col-sm-10">
                            {!! Form::input(':argLink', Arr::get($_POST, ':argLink'), [
                                'class' => 'form-control', 'type' => 'url', 'required'
                            ]) !!}
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" name="create" title="Create" form="addLink">Create</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#select_all').click(function(event){
            if(this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            }else{
                $(':checkbox').each(function(){
                    this.checked = false;
                });
            }
        });
        
        var $scopeModal = $('#myModal');
        var $argExpiration = $(':input[name=":argExpiration"]');
        var $expirationHours = $('#expiration-hours');
        var $expirationMinutes = $('#expiration-minutes');

        $expirationHours.val($argExpiration.val() ? Math.floor(parseInt($argExpiration.val(), 10) / 60) : '');
        $expirationMinutes.val($argExpiration.val() ? parseInt($argExpiration.val(), 10) % 60 : '');
        
        var updateExpiration = function() {
            if ( '' == $expirationHours.val() ) {
                $expirationHours.val('0');
            }
            if ( '' == $expirationMinutes.val() ) {
                $expirationMinutes.val('0');
            }
            var expirationInMinutes = parseInt($expirationHours.val(), 10) * 60 + parseInt($expirationMinutes.val(), 10);
            $argExpiration.val(expirationInMinutes);
        };
        
        $expirationHours.on('change keydown mousedown', function(e) {
            updateExpiration();
        });
        $expirationMinutes.on('change keydown mousedown', function(e) {
            updateExpiration();
        });
        
        $('[data-toggle="tooltip"]', $scopeModal).tooltip();
    });
</script>