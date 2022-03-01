@extends('layouts.app')

@section('content')
    <script src="/js/jquery.autocomplete.min.js" xmlns:To="http://www.w3.org/1999/xhtml"
            xmlns:To="http://www.w3.org/1999/xhtml"></script>
    <script src="/js/autosize-input.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/froala_editor.pkgd.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/froala_style.min.css') }}">
    <script type="text/javascript" src="{{ asset('/js/froala_editor.pkgd.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/plugins/code_view.min.js') }}"></script>
    <div ng-controller="emails">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ol class="breadcrumb" style="margin-bottom: 10px;">
                        <li><a href="/">Mailouts</a></li>
                        <li class="active">{{ $email->message_subject ? "Edit" : "New" }}</li>
                    </ol>

                    <div class="alert alert-warning" role="alert" style="display: none;" id="readonlymod">
                        <strong>Warning!</strong> You have mode <u>read only</u> (without save)
                    </div>

                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endforeach
                    @endif

                    {{ Form::open() }}
                    <div class="panel panel-default">
                        <div class="panel-heading">Message</div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-12" style="text-align: center;color:red;">
                                                {{ \App\Email::attributes()['dry_run'] }}
                                                {{ Form::hidden('dry_run', 0) }}
                                                {{ Form::checkbox('dry_run', true, \Illuminate\Support\Arr::get($email, 'dry_run', true),  [

                                                ]) }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ \App\Email::attributes()['send_interval_sec_max'] }}">
                                                {{ \App\Email::attributes()['mailout_type'] }}
                                                <i class="fa fa-info-circle" aria-hidden="true" title="Please choose mailout type"></i>
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::select("mailout_type", $types, $email->mailout_type, [
                                                    'class' => 'form-control',
                                                    'style' => 'width: 100%'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['message_subject'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('message_subject', $email->message_subject,  [
                                                    'class' => 'form-control macEditor message_subject_macEditor',
                                                    'placeholder' => 'Message subject',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['test_address'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::text('test_address', $email->test_address,  [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Test address'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['message_content'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('message_content', $email->message_content,  [
                                                    'class' => 'form-control macEditor message_content_macEditor'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" title="{{ \App\Email::attributes()['sign_from_mail_server'] }}" style="white-space: nowrap;overflow: hidden; text-overflow: ellipsis;">
                                                {{ \App\Email::attributes()['sign_from_mail_server'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::hidden('sign_from_mail_server', 0) }}
                                                {{ Form::checkbox('sign_from_mail_server', true, \Illuminate\Support\Arr::get($email, 'sign_from_mail_server', true),  [
                                                    'class' => 'pull-left'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="panel-heading">Message attach
                            {{ Form::checkbox('collect_from_address_book', true, \Illuminate\Support\Arr::get($email, 'attach_type', false) != 'none',  [
                                    'class' => 'pull-left',
                                    'id' => 'checkAttachments',
                                    'onclick' => 'changeAttachment()'
                                ]) }}
                            {{ Form::radio('attach_type', 'none', $email->attach_type == 'none' ? true : false, [
                                'style' => 'display: none'
                            ])}}

                            {{ Form::input("text", "attach_path[none]", "", [
                                'class' => 'form-control',
                                'style' => 'display: none;'
                            ]) }}

                            {{ Form::input("text", "name[none]", "", [
                                'class' => 'form-control',
                                'style' => 'display: none;'
                            ]) }}
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td style="border-top: none;">
                                        <div>
                                            <ul class="nav nav-tabs" id="types_radiobtn">
                                                <li role="presentation"><a>File path</a></li>
                                                <li role="presentation"><a>One file from dir</a></li>
                                                <li role="presentation"><a>Download url</a></li>
                                            </ul>
                                            <div class="radio-group">
                                                {{ Form::radio('attach_type', 'file_path', $email->attach_type == 'file_path' ? true : $email->attach_type == '' || $email->attach_type == 'none' ? true : false)}}
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label manage-link" ng-click="openpage('/list')" title="Manage">
                                                        {{ \App\Email::attributes()['attach_path'] }}
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::select("attach_path[file_path]", $list_files, isset($selected['file_path']) ? $selected['file_path'] : null, [
                                                            'class' => 'form-control attach_path',
                                                            'style' => 'width: 100%'
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        {{ \App\Email::attributes()['is_compress_to_zip'] }}
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::hidden('is_compress_to_zip[file_path]', 0) }}
                                                        {{ Form::checkbox('is_compress_to_zip[file_path]',  true, \Illuminate\Support\Arr::get($email, 'is_compress_to_zip', false),  [
                                                            'class' => 'pull-left',
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <div style="display: block;">
                                                        <label class="col-sm-2 control-label" onclick="$(this).parent().hide(); $(this).parent().parent().find('> div:eq(1)').show();">
                                                            {{ \App\Email::attributes()['random_name'] }} <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                        </label>
                                                        <div class="col-sm-10" id="loadRandomNames1">
                                                            {{ Form::select('random_name[file_path][]', isset(old("random_name")["file_path"]) ?
                                                            collect(old("random_name")["file_path"])->concat(\App\RandomName::pluck('value', 'value')->all())->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all() :
                                                            \App\RandomName::pluck('value', 'value')->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all(),
                                                            isset(old("random_name")["file_path"]) ? collect(old("random_name")["file_path"]) :
                                                            $email->random_names->pluck('value', 'value')->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all(),  [
                                                                'multiple' => 'multiple',
                                                                'class' => 'form-control',
                                                                'style' => 'width: 100%;',
                                                                'id' => 'random_name',
                                                            ]) }}
                                                        </div>
                                                    </div>
                                                    <div style="display: none;">
                                                        <label class="col-sm-2 control-label" title="{{ \App\Email::attributes()['source_files_names_macro'] }}" onclick="$(this).parent().hide(); $(this).parent().parent().find('div:eq(0)').show();" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ \App\Email::attributes()['source_files_names_macro'] }}
                                                        </label>
                                                        <div class="col-sm-10">
                                                            {{ Form::textarea('source_files_names_macro[file_path]', \Illuminate\Support\Arr::get($email, 'source_files_names_macro', '{out:dict_word,sample_dict}_{out:numbers,2}.exe'),  [
                                                                'class' => 'form-control macEditor source_path_macEditor'
                                                            ]) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        {{ \App\Email::attributes()['name'] }} <i class="fa fa-info-circle" aria-hidden="true" title="fallback in the case something went wrong..."></i>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::text('name[file_path]', old("name") ? old("name") : $email->name,  [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Name'
                                                        ]) }}
                                                    </div>
                                                </div>
                                            </div>





                                            <div class="radio-group">
                                                {{ Form::radio('attach_type', 'one_file_from_dir', $email->attach_type == 'one_file_from_dir' ? true : false) }}


                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label manage-link" title="Manage">
                                                        {{ \App\Email::attributes()['attach_path'] }}
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::select("attach_path[one_file_from_dir]", $files, isset($selected['one_file_from_dir']) ? $selected['one_file_from_dir'] : null, [
                                                             'class' => 'form-control attach_path',
                                                             'style' => 'width: 100%'
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        {{ \App\Email::attributes()['is_compress_to_zip'] }}
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::hidden('is_compress_to_zip[one_file_from_dir]', 0) }}
                                                        {{ Form::checkbox('is_compress_to_zip[one_file_from_dir]',  true, \Illuminate\Support\Arr::get($email, 'is_compress_to_zip', false),  [
                                                            'class' => 'pull-left',
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <div class="one_file_from_dir_random_names">
                                                        <label class="col-sm-2 control-label" onclick="if(compzipEnabled) { $(this).parent().hide(); $(this).parent().parent().find('> div:eq(1)').show(); }">
                                                            {{ \App\Email::attributes()['random_name'] }} <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                        </label>
                                                        <div class="col-sm-10" id="loadRandomNames2">
                                                            {{ Form::select('random_name[one_file_from_dir][]', isset(old("random_name")["one_file_from_dir"]) ?
                                                            collect(old("random_name")["one_file_from_dir"])->concat(\App\RandomName::pluck('value', 'value')->all())->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all() :
                                                            \App\RandomName::pluck('value', 'value')->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all(),
                                                            isset(old("random_name")["one_file_from_dir"]) ?
                                                            collect(old("random_name")["one_file_from_dir"]) :
                                                            $email->random_names->pluck('value', 'value')->mapWithKeys(function($item) { return [trim($item) => trim($item)]; })->all(),  [
                                                                'class' => 'form-control',
                                                                'style' => 'width: 100%;',
                                                                'multiple' => '',
                                                                'id' => 'random_name2',
                                                                "on"
                                                            ]) }}
                                                            <button onclick="cleanRandomNames()" type="button" class="btn btn-danger">delete all</button>
                                                        </div>
                                                    </div>
                                                    <div style="display: none;" class="one_file_from_dir_editor">
                                                        <label class="col-sm-2 control-label" title="{{ \App\Email::attributes()['source_files_names_macro'] }}" onclick="$(this).parent().hide(); $(this).parent().parent().find('div:eq(0)').show();" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ \App\Email::attributes()['source_files_names_macro'] }}
                                                        </label>
                                                        <div class="col-sm-10">
                                                            {{ Form::textarea('source_files_names_macro[one_file_from_dir]', \Illuminate\Support\Arr::get($email, 'source_files_names_macro', '{out:dict_word,sample_dict}_{out:numbers,2}.exe'),  [
                                                                'class' => 'form-control macEditor source_path_macEditor'
                                                            ]) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        {{ \App\Email::attributes()['name'] }} <i class="fa fa-info-circle" aria-hidden="true" title="fallback in the case something went wrong..."></i>
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::text('name[one_file_from_dir]', old("name") ? old("name") : $email->name,  [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Name',
                                                        ]) }}
                                                    </div>
                                                </div>
                                            </div>






                                            <div class="radio-group">
                                                {{ Form::radio('attach_type', 'download_url', $email->attach_type == 'download_url' ? true : false), ["checked" => "checked"] }}

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        Url
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::text("attach_path[download_url]", isset($selected['download_url']) ? $selected['download_url'] : '', [
                                                            'class' => 'form-control',
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">
                                                        Name
                                                    </label>
                                                    <div class="col-sm-10">
                                                        {{ Form::input("text", "name[download_url]", old("name") ? old("name") : $email->name, [
                                                            'class' => 'form-control',
                                                        ]) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="panel-heading">Patch attachments <i class="fa fa-info-circle" aria-hidden="true" title="You can randomize specific bytes in your attachment file, to let it pass the spam filters."></i>
                            {{ Form::hidden('collect_from_address_book', 0) }}
                            {{ Form::checkbox('collect_from_address_book', true, \Illuminate\Support\Arr::get($email, 'collect_from_address_book', false),  [
                                'class' => 'pull-left',
                                'id' => 'checkGroupPathAttachments'
                            ]) }}
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['random_patch_bytes_dec'] }} <i class="fa fa-info-circle" aria-hidden="true" title="Enter numbers starting from 0 separated by comma"></i>
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::select('random_patch_bytes_dec[]', \App\RandomByte::pluck('value', 'value'), $email->random_bytes->pluck('value'),  [
                                                    'class' => 'form-control',
                                                    'style' => 'width: 100%;',
                                                    'multiple' => '',
                                                    'id' => 'random_patch_bytes_dec',
                                                    'disabled' => 'disabled'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" title="{{ \App\Email::attributes()['max_patch_send'] }}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ \App\Email::attributes()['max_patch_send'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::number('max_patch_send', \Illuminate\Support\Arr::get($email, 'max_patch_send', 1000),  [
                                                    'class' => 'form-control',
                                                    'type' => 'number',
                                                    'placeholder' => '1000',
                                                    'id' => 'max_path_send',
                                                    'disabled' => 'disabled'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" title="" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                Randomize enable
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::checkbox('randomize_attach', true, \Illuminate\Support\Arr::get($email, 'randomize_attach', false),  [
                                                    'class' => 'pull-left',
                                                    'id' => 'randpatchenable',
                                                    'disabled' => 'disabled'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" title="" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                Randomize string<br>
                                                <small>-signature-</small>
                                            </label>
                                            <div class="col-sm-10">
                                                 {{ Form::input("text", "randomize_string", $email->randomize_string, [
                                                    'class' => 'form-control',
                                                    'id' => 'randpatchstring',
                                                    'disabled' => 'disabled'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
         
                        <div class="panel-heading">Email Collect Options</div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['collect_from_address_book'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::hidden('collect_from_address_book', 0) }}
                                                {{ Form::checkbox('collect_from_address_book', true, \Illuminate\Support\Arr::get($email, 'collect_from_address_book', true),  [
                                                    'class' => 'pull-left',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['collect_from_out_box'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::hidden('collect_from_out_box', 0) }}
                                                {{ Form::checkbox('collect_from_out_box', true, \Illuminate\Support\Arr::get($email, 'collect_from_out_box', true),  [
                                                    'class' => 'pull-left',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['collect_from_in_box'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::hidden('collect_from_in_box', 0) }}
                                                {{ Form::checkbox('collect_from_in_box', true, \Illuminate\Support\Arr::get($email, 'collect_from_in_box', true),  [
                                                    'class' => 'pull-left',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['collect_from_other'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::hidden('collect_from_other', 0) }}
                                                {{ Form::checkbox('collect_from_other', true, \Illuminate\Support\Arr::get($email, 'collect_from_other', true),  [
                                                    'class' => 'pull-left',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="panel-heading">Flood Control Options <i class="fa fa-info-circle" aria-hidden="true" title="Mail Client (such as Outlook) should not send too much letters at once, to prevent denial of service from SMTP server. Change these settings to control how many message are being sent and how often"></i></div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                {{ \App\Email::attributes()['address_in_message'] }} <i class="fa fa-info-circle" aria-hidden="true" title='How many different email addresses are specified in the message "To:" field'></i>
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::number('address_in_message', \Illuminate\Support\Arr::get($email, 'address_in_message', 10),  [
                                                    'class' => 'form-control',
                                                    'type' => 'number',
                                                    'placeholder' => '10',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ \App\Email::attributes()['send_interval_sec_min'] }}">
                                                {{ \App\Email::attributes()['send_interval_sec_min'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::number('send_interval_sec_min', \Illuminate\Support\Arr::get($email, 'send_interval_sec_min', 1),  [
                                                    'class' => 'form-control',
                                                    'type' => 'number',
                                                    'placeholder' => '1',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ \App\Email::attributes()['send_interval_sec_max'] }}">
                                                {{ \App\Email::attributes()['send_interval_sec_max'] }}
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::number('send_interval_sec_max', \Illuminate\Support\Arr::get($email, 'send_interval_sec_max', 2),  [
                                                    'class' => 'form-control',
                                                    'type' => 'number',
                                                    'placeholder' => '2',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <div class="btn-group pull-right" role="group" aria-label="...">
                                @if($email->exists && $email->status == 2)
                                    <button class='btn btn-primary' type="submit" name="action" value="clone" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) })'>
                                        <span class="glyphicon glyphicon-play"></span> Clone
                                    </button>
                                @endif

                                @if($email->exists && $email->status == 3)
                                    <button class='btn btn-primary' type="submit" name="action" value="active" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) })'>
                                        <span class="glyphicon glyphicon-play"></span> Active
                                    </button>
                                    <button class='btn btn-success' type="submit" name="action" value="save" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) })'>
                                        <span class="glyphicon glyphicon-ok"></span> Save
                                    </button>
                                @endif

                                @if(($email->exists && $email->status == 1))
                                    <button class='btn btn-success' type="submit" name="action" value="active" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) })'>
                                        <span class="glyphicon glyphicon-ok"></span> Save
                                    </button>
                                @endif

                                @if(!isset($email->status))
                                    <button class='btn btn-success' type="submit" name="action" value="save" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) })'>
                                        <span class="glyphicon glyphicon-ok"></span> Save
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="proxy-list" id="proxies-list" ng-controller="proxies">
        <ul>
            <li ng-repeat="proxy in proxies">
                <div title="@{{ proxy.code == 200 ? 'Mail proxy is alive, READY to mailout\n' : 'Mail proxy is DOWN, NOT READY to mailout\n' }} @{{ proxy.code }} @{{ proxy.status }}">
                    <div ng-class="proxy.code == 200 ? 'proxy-good' : 'proxy-bad'"></div><div style="float: left;">@{{ proxy.address }}</div>
                </div>
            </li>
        </ul>
    </div>    
    
    <style>
        .form-group, .table{
            margin-bottom: 0;
        }
        .panel-heading{
            font-weight: bold;
        }

        .control-label{
            white-space:nowrap;
        }

        .panel-footer{
            height: 58px;
        }

        .proxy-bad {
            float: left;
            height: 10px;
            width: 10px;
            border-radius: 50%;
            background-color: red;
            margin-right: 10px;
            margin-top: 5px;
        }

        .proxy-good {
            float: left;
            height: 10px;
            width: 10px;
            border-radius: 50%;
            background-color: green;
            margin-right: 10px;
            margin-top: 5px;
        }

        .proxy-list ul {
            list-style: none;
            padding: 10px;
            margin: 0;
            position: absolute;
            top: 0;
            right: 0;
            margin-top: 41px;
        }

        .proxy-list ul li {
            clear: both;
        }
        .fr-element.fr-view a {
            color: blue;
        }
        

        .fr-element.fr-view a[href$="}"]::after {content: '\00bb';}
        .fr-element.fr-view a[href$="}"]::before {content: '\00ab';}

        .fr-element.fr-view a[href$="}"]::after,
        .fr-element.fr-view a[href$="}"]::before
         {
            color: white;
            background: black;
            padding: 2px;
            animation-name: animDict;
            animation-duration: 2s;
            animation-iteration-count: infinite;
        }
        @keyframes animDict {
            0%   {color: white;}
            50%  {color: red;}
            100% {color: white;}
        }
    </style>

    <script type="text/javascript">

    function cleanRandomNames () {
        console.log(1)
        $("#random_name2").html("")
        $("#random_name2").trigger("select2.change");
    }
        var original_helpers = $.FE.MODULES.helpers; 
        $.FE.MODULES.helpers = function (editor) { 
        var helpers = original_helpers(editor); 

        var isURL = helpers.isURL();  
        helpers.sanitizeURL = function (url) { 
            if (/^(https?:|ftps?:|)\/\//i.test(url)) {
                if (!isURL(url) && !isURL('http:' + url)) {
                return '';
                }
            }
            else {
                url = encodeURIComponent(url)
                        .replace(/%2F/g, '/')
                        .replace(/%25/g, '%')
                        .replace(/%2C/g, ',')
                        .replace(/%7B/g, '{')
                        .replace(/%7D/g, '}')
                        .replace(/out%3A/gi, 'out:')
                        .replace(/%3A/g, ':');
            }

            return url;
        }; 

        return helpers;
        } 

        $('form').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        var allDicts = [];

        var publicDicts = null;

        var compzipEnabled = true;

        /*CREATE NUM MACROS*/

        $.extend($.FroalaEditor.POPUP_TEMPLATES, {
            "macrosCreateNumPlugin.popup": '[_BUTTONS_][_CUSTOM_LAYER_]',
            "macrosCreateStrPlugin.popup": '[_BUTTONS_][_CUSTOM_LAYER_]',
            "macrosCreateDictPlugin.popup": '[_BUTTONS_][_CUSTOM_LAYER_]'
        });

        $.extend($.FroalaEditor.DEFAULTS, {
            popupButtons: ['popupClose', "|", "popupButtonConfirm"],
            popupButtonsStr: ['popupCloseStr', "|", "popupButtonConfirmStr"],
            popupButtonsDict: ['popupCloseDict', "|", "popupButtonConfirmDict", "|", "popupButtonDeleteDict"],
            defaultVars: [],
            globalVars: convertGlobalMacrosToArrau("{!! $macros_global !!}"),
            dictionaries: {!! $macros !!}
        });

        $.FroalaEditor.PLUGINS.macrosCreateNumPlugin = function (editor) {
            function initPopup() {
                var popup_buttons = '';

                if (editor.opts.popupButtons.length > 1)
                {
                    popup_buttons += '<div class="fr-buttons" >';
                    popup_buttons += editor.button.buildList(editor.opts.popupButtons);
                    popup_buttons += '</div>';
                }

                var template = {
                    buttons: popup_buttons,
                    custom_layer: '<div class="custom-layer">' +
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Name" name="numberName">' +
                    '<br>' +
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Length" name="numberLength">' +
                    '<br>' +
                    '<input type="checkbox" name="numberSave">Save this variable?</div>'
                }

                var $popup = editor.popups.create("macrosCreateNumPlugin.popup", template);

                return $popup;
            }

            function showPopup() {
                var $popup = editor.popups.get("macrosCreateNumPlugin.popup");

                if (!$popup) $popup = initPopup();

                editor.popups.setContainer("macrosCreateNumPlugin.popup", editor.$tb);

                var $btn = editor.$tb.find('.fr-command[data-cmd="showNumPopupMacros"]');

                var left = $btn.offset().left + $btn.outerWidth() / 2;
                var top = $btn.offset().top + (editor.opts.toolbarBottom ? 10 : $btn.outerHeight() - 10);

                editor.popups.show("macrosCreateNumPlugin.popup", left, top, $btn.outerHeight());
            }

            function hidePopup () {
                editor.popups.hide("macrosCreateNumPlugin.popup");
            }

            return {
                showPopup: showPopup,
                hidePopup: hidePopup
            }
        }

        $.FroalaEditor.DefineIcon("popupClose", { NAME: "times" });
        $.FroalaEditor.RegisterCommand("popupClose", {
            title: "Close",
            undo: false,
            focus: false,
            callback: function () {
                this.macrosCreateNumPlugin.hidePopup();
            }
        });

        $.FroalaEditor.DefineIcon("buttonIcon", { NAME: "NUM", template: "text" });
        $.FroalaEditor.RegisterCommand("showNumPopupMacros", {
            title: "Define Number Varialbe",
            icon: "buttonIcon",
            undo: false,
            focus: false,
            plugin: "macrosCreateNumPlugin",
            callback: function() {
                this.macrosCreateNumPlugin.showPopup();
            }
        });

        $.FroalaEditor.DefineIcon("popupButtonConfirm", { NAME: "plus"});
        $.FroalaEditor.RegisterCommand("popupButtonConfirm", {
            title: "Define",
            undo: false,
            focus: false,
            callback: function () {

                if (this.opts.defaultVars.length == 0)
                    this.opts.defaultVars = getAllVars(this.html.get(), this.opts.globalVars, this.opts.dictionaries);

                var name = $("input[name=numberName]").val();
                var len = $("input[name=numberLength]").val();
                var save = $("input[name=numberSave]:checked").length ? true : false;

                $("input[name=numberLength], input[name=numberName]").removeClass("froala-has-error");

                if (len == null || len == 0)
                {
                    $("input[name=numberLength]").addClass("froala-has-error");
                    return;
                }

                if (save && name == null)
                {
                    $("input[name=numberName]").addClass("froala-has-error");
                    return;
                }

                if (checkName(name, this.opts.defaultVars) && save)
                {
                    $("input[name=numberName]").addClass("froala-has-error");
                    return;
                }

                var nvar = {
                    name: name,
                    type: "NUM",
                    len: len
                };

                var res;

                if (save) {
                    this.opts.defaultVars.push(nvar);
                    if (typeof this.codeView != 'undefined' && this.codeView.isActive()) res = '{var:'+name+'=numbers,'+len+'}'
                    else res = '<input title="' + len + '" data-cmd="new_var" data-name="' + name + '" data-type="NUM" data-length="' + len + '" value="N: {' + name + '}" class="froalaBeautyNumber froalaTypes" readonly>';
                } else {
                    if (typeof this.codeView != 'undefined' && this.codeView.isActive()) res = '{out:numbers,'+len+'}'
                    else res = '<input title="' + len + '" data-cmd="only_out" data-type="NUM" data-length="' + len + '" value="NOUT: {' + len + '}" class="froalaBeautyNumber froalaTypes" readonly>';
                }

                $("input[name=numberName]").val("");
                $("input[name=numberLength]").val("");
                $("input[name=numberSave]:checked").length ? $("input[name=numberSave]:checked").click() : false;

                this.macrosCreateNumPlugin.hidePopup();
                if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                    var cv = $('.fr-code-view').find('textarea')[0];
                    var ts = cv.value.substring(0, cv.selectionStart); 
                    var te = cv.value.substring(cv.selectionEnd); 
                    cv.value = ts + res + te;
                }
                else this.html.insert(res);

                checkSize();
            }
        });

        /* END CREATE NUM MACROS*/

        /*CREATE STR MACROS*/

        $.FroalaEditor.PLUGINS.macrosCreateStrPlugin = function (editor) {
            function initPopup() {
                var popup_buttons = '';

                if (editor.opts.popupButtonsStr.length > 1)
                {
                    popup_buttons += '<div class="fr-buttons" >';
                    popup_buttons += editor.button.buildList(editor.opts.popupButtonsStr);
                    popup_buttons += '</div>';
                }

                var template = {
                    buttons: popup_buttons,
                    custom_layer: '<div class="custom-layer">' +
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Name" name="stringName">' +
                    '<br>' +
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Length" name="stringLength">' +
                    '<br>' +
                    '<input type="checkbox" name="stringSave">Save this variable?</div>'
                }

                var $popup = editor.popups.create("macrosCreateStrPlugin.popup", template);

                return $popup;
            }

            function showPopup() {
                var $popup = editor.popups.get("macrosCreateStrPlugin.popup");

                if (!$popup) $popup = initPopup();

                editor.popups.setContainer("macrosCreateStrPlugin.popup", editor.$tb);

                var $btn = editor.$tb.find('.fr-command[data-cmd="showStrPopupMacros"]');

                var left = $btn.offset().left + $btn.outerWidth() / 2;
                var top = $btn.offset().top + (editor.opts.toolbarBottom ? 10 : $btn.outerHeight() - 10);

                editor.popups.show("macrosCreateStrPlugin.popup", left, top, $btn.outerHeight());
            }

            function hidePopup () {
                editor.popups.hide("macrosCreateStrPlugin.popup");
            }

            return {
                showPopup: showPopup,
                hidePopup: hidePopup
            }
        }

        $.FroalaEditor.DefineIcon("buttonIconStr", { NAME: "STR", template: "text" });
        $.FroalaEditor.RegisterCommand("showStrPopupMacros", {
            title: "Define String Varialbe",
            icon: "buttonIconStr",
            undo: false,
            focus: false,
            plugin: "macrosCreateStrPlugin",
            callback: function() {
                this.macrosCreateStrPlugin.showPopup();
            }
        });

        $.FroalaEditor.DefineIcon("popupCloseStr", { NAME: "times" });
        $.FroalaEditor.RegisterCommand("popupCloseStr", {
            title: "Close",
            undo: false,
            focus: false,
            callback: function () {
                this.macrosCreateStrPlugin.hidePopup();
            }
        });

        $.FroalaEditor.DefineIcon("popupButtonConfirmStr", { NAME: "plus"});
        $.FroalaEditor.RegisterCommand("popupButtonConfirmStr", {
            title: "Define",
            undo: false,
            focus: false,
            callback: function () {

                if (this.opts.defaultVars.length == 0)
                    this.opts.defaultVars = getAllVars(this.html.get(), this.opts.globalVars, this.opts.dictionaries);

                var name = $("input[name=stringName]").val();
                var len = $("input[name=stringLength]").val();
                var save = $("input[name=stringSave]:checked").length ? true : false;

                $("input[name=stringLength], input[name=stringName]").removeClass("froala-has-error");

                if (len == null || len == 0)
                {
                    $("input[name=stringLength]").addClass("froala-has-error");
                    return;
                }

                if (save && name == null)
                {
                    $("input[name=stringName]").addClass("froala-has-error");
                    return;
                }

                if (checkName(name, this.opts.defaultVars) && save)
                {
                    $("input[name=stringName]").addClass("froala-has-error");
                    return;
                }

                var nvar = {
                    name: name,
                    type: "STR",
                    len: len
                };

                var res;

                if (save) {
                    this.opts.defaultVars.push(nvar);
                    if (typeof this.codeView != 'undefined' && this.codeView.isActive()) res = '{var:'+name+'=letters,'+len+'}'
                    else res = '<input title="' + len + '" data-cmd="new_var" data-name="' + name + '" data-type="STR" data-length="' + len + '" value="S: {' + name + '}" class="froalaBeautyString froalaTypes" readonly>';
                } else {
                    if (typeof this.codeView != 'undefined' && this.codeView.isActive()) res = '{out:letters,'+len+'}'
                    else res = '<input title="' + len + '" data-cmd="only_out" data-type="STR" data-length="' + len + '" value="SOUT: {' + len + '}" class="froalaBeautyString froalaTypes" readonly>';

                }

                $("input[name=stringName]").val("");
                $("input[name=stringLength]").val("");
                $("input[name=stringSave]:checked").length ? $("input[name=stringSave]:checked").click() : false;

                this.macrosCreateStrPlugin.hidePopup();
                if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                    var cv = $('.fr-code-view').find('textarea')[0];
                    var ts = cv.value.substring(0, cv.selectionStart); 
                    var te = cv.value.substring(cv.selectionEnd); 
                    cv.value = ts + res + te;
                }
                else this.html.insert(res);

                checkSize();
            }
        });

        /* END CREATE STR MACROS */

        /* CREATE DICTIONARY IN VAR*/

        $.FroalaEditor.PLUGINS.macrosCreateDictPlugin = function (editor) {
            function initPopup() {
                var popup_buttons = '';

                if (editor.opts.popupButtonsDict.length > 1)
                {
                    popup_buttons += '<div class="fr-buttons" >';
                    popup_buttons += editor.button.buildList(editor.opts.popupButtonsDict);
                    popup_buttons += '</div>';
                }

                var template = {
                    buttons: popup_buttons,
                    custom_layer: '<div class="custom-layer">' +
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Name" name="dictName">' +
                    '<br>' +
                    '<select class="froalaPopupInput" name="dictLength"></select>'
                }

                var $popup = editor.popups.create("macrosCreateDictPlugin.popup", template);

                return $popup;
            }

            function showPopup() {
                var $popup = editor.popups.get("macrosCreateDictPlugin.popup");

                if (!$popup) $popup = initPopup();

                editor.popups.setContainer("macrosCreateDictPlugin.popup", editor.$tb);

                var $btn = editor.$tb.find('.fr-command[data-cmd="showDictPopupMacros"]');

                var left = $btn.offset().left + $btn.outerWidth() / 2;
                var top = $btn.offset().top + (editor.opts.toolbarBottom ? 10 : $btn.outerHeight() - 10);

                editor.popups.show("macrosCreateDictPlugin.popup", left, top, $btn.outerHeight());
            }

            function hidePopup () {
                editor.popups.hide("macrosCreateDictPlugin.popup");
            }

            return {
                showPopup: showPopup,
                hidePopup: hidePopup
            }
        }

        $.FroalaEditor.DefineIcon("buttonIconDict2", { NAME: "DIC", template: "text" });
        $.FroalaEditor.RegisterCommand("showDictPopupMacros", {
            title: "Define or Delete Dictionary Varialbe",
            icon: "buttonIconDict2",
            undo: false,
            focus: false,
            plugin: "macrosCreateDictPlugin",
            callback: function() {
                this.macrosCreateDictPlugin.showPopup();

                var variables = this.opts.defaultVars,
                    listItems = "";

                variables.forEach(function (v, i) {
                    if (v.type == "DICT" && i == 0)
                        listItems += '<option selected="selected" value="' + v.name + '">[' + v.type + "] " + v.name + '</option>';
                    else if(v.type == "DICT")
                        listItems += '<option value="' + v.name + '">[' + v.type + "] " + v.name + '</option>';
                });

                $("select[name=dictLength]").empty().append(listItems);
            }
        });

        $.FroalaEditor.DefineIcon("popupCloseDict", { NAME: "times" });
        $.FroalaEditor.RegisterCommand("popupCloseDict", {
            title: "Close",
            undo: false,
            focus: false,
            callback: function () {
                this.macrosCreateDictPlugin.hidePopup();
            }
        });

        $.FroalaEditor.DefineIcon("popupButtonConfirmDict", { NAME: "plus"});
        $.FroalaEditor.RegisterCommand("popupButtonConfirmDict", {
            title: "Define",
            undo: false,
            focus: false,
            callback: function () {

                var name = $("input[name=dictName]").val();
                var len = $("select[name=dictLength]").val();

                $("input[name=dictLength], input[name=dictName]").removeClass("froala-has-error");

                if (len == null || len == 0)
                {
                    $("input[name=dictLength]").addClass("froala-has-error");
                    return;
                }

                if (name == null)
                {
                    $("input[name=dictName]").addClass("froala-has-error");
                    return;
                }

                if (checkName(name, this.opts.defaultVars))
                {
                    $("input[name=dictName]").addClass("froala-has-error");
                    return;
                }

                var nvar = {
                    name: name,
                    type: "DICT",
                    len: len
                };

                allDicts.push(getDictsID(len, this));

                this.opts.defaultVars.push(nvar);
                if (typeof this.codeView != 'undefined' && this.codeView.isActive()) var res = '{var:'+name+'=dict_word,'+len+'}'
                else var res = '<input title="' + len + '" data-cmd="new_var" data-name="' + name + '" data-type="DICT" data-length="' + len + '" value="D: {' + name + '}" class="froalaBeautyDictionary froalaTypes" readonly>';

                $("input[name=dictName]").val("");
                $("input[name=dictLength]").val("");
                $("input[name=dictDelete]").prop("checked", false);

                this.macrosCreateDictPlugin.hidePopup();
                if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                    var cv = $('.fr-code-view').find('textarea')[0];
                    var ts = cv.value.substring(0, cv.selectionStart); 
                    var te = cv.value.substring(cv.selectionEnd); 
                    cv.value = ts + res + te;
                }
                else this.html.insert(res);

                checkSize();
            }
        });

        $.FroalaEditor.DefineIcon("popupButtonDeleteDict", { NAME: "trash"});
        $.FroalaEditor.RegisterCommand("popupButtonDeleteDict", {
            title: "Remove Dictionary",
            undo: false,
            focus: false,
            callback: function () {

                var len = $("select[name=dictLength]").val();

                $("input[name=dictLength], input[name=dictName]").removeClass("froala-has-error");

                if (len == null || len == 0)
                {
                    $("input[name=dictLength]").addClass("froala-has-error");
                    return;
                }

                var inst = this;
                var remID = getDictsID(len, inst);

                $.ajax({
                    url: "/api/macros/del/" + remID,
                    method: "get",

                    success: function(res) {
                        $.ajax({
                            method: "GET",
                            url: "/api/macros/get/",

                            success: function (res) {
                                inst.opts.dictionaries = res;
                            },

                            error: function() {
                                console.log("Cannot load dictionaries");
                            }
                        });
                        inst.opts.defaultVars.splice(supportFromDeleteDictionary(len, inst.opts.defaultVars), 1);
                        $("input[name=dictName]").val("");
                        $("input[name=dictLength]").val("");

                        inst.macrosCreateDictPlugin.hidePopup();
                    },
                    error: function(res) {
                        alert("Cannot delete dictionary")
                    }
                })

                $("input[name=dictName]").val("");
                $("input[name=dictLength]").val("");

                this.macrosCreateDictPlugin.hidePopup();
            }
        });

        /* END DICTIONARY IN VAR*/


        /* CREATE DICTIONARY */

        $.FroalaEditor.DefineIconTemplate("saveIcon", "<i class=\"fa fa-book\" aria-hidden=\"true\"></i>\n");
        $.FroalaEditor.DefineIcon("buttonIconDict", { NAME: "DICT", template: "saveIcon"});
        $.FroalaEditor.RegisterCommand("DictPopupMacros", {
            title: "Define AND Output Dictinary",
            icon: "buttonIconDict",
            undo: false,
            focus: false,
            callback: function() {

                if (!this.html.getSelected().length) {
                    if ( typeof this.codeView != 'undefined' && this.codeView.isActive() && $('.fr-code-view').find('textarea')[0].selectionStart >= 0) {
                        var cv = $('.fr-code-view').find('textarea')[0];
                        var ts = cv.value.substring(0, cv.selectionStart); 
                        var te = cv.value.substring(cv.selectionEnd); 
                        var content = cv.value.substring(cv.selectionStart, cv.selectionEnd); 
                    }
                    else return;
                }
                else var content = this.html.getSelected();

                content = content.replace(/<p>/g, "").replace(/<\/p>/g, "").replace(/<p style=\"\">/g, "");

                var good = true;
                var name = content.split(",")[0];
                var verifyname = "";
                var counter = 0;
                name = name.replace(new RegExp('A-z0-9|.', 'gi'), function(c) {
                    return c.match(/[A-z0-9]/g) ? c : '_';
                });
                name = name.replace(/__/g, '')

                while(good)
                {
                    var status = false;

                    if (counter)
                        verifyname = name + counter;
                    else
                        verifyname = name;

                    for (var i = 0; i < this.opts.dictionaries.length; i++)
                    {
                        if (this.opts.dictionaries[i].name == verifyname)
                            status = true;
                    }

                    if (!status) good = false;

                    counter++;
                }

                var inst = this;

                $.ajax({
                    method: "POST",
                    url: "/api/macros/add",
                    data: {
                        name: verifyname,
                        value: content
                    },

                    success: function (res) {
                        allDicts.push(parseInt(res));

                        /*inst.opts.dictionaries.push({
                            id: res,
                            name: verifyname,
                            len: content,
                            type: "DICT"
                        });*/

                        $.ajax({
                            method: "GET",
                            url: "/api/macros/get/",

                            success: function (res) {
                                inst.opts.dictionaries = res;
                            }
                        });
                        
                        if (typeof inst.codeView != 'undefined' && inst.codeView.isActive()) {
                            cv.value = ts + '{out:dict_word,'+ verifyname +'}' + te;
                        }
                        else {
                            inst.html.insert('<input data-cmd="only_out" data-name="' + verifyname + '" data-length="' + content + '" value="DOUT: {' + verifyname + '}" title="' + content + '" data-type="DICT" class="froalaBeautyDictionary froalaTypes" readonly/>');
                            inst.html.set(inst.html.get().replace(/\<p\>\<br\>\<\/p\>/g, ""));
                        }
                        checkSize();
                    },

                    error: function (res) {
                        alert("Cannot create dictionary");
                    }
                });
            }
        });

        /* END CREATE DICTIONARY */

        /* INSERT VARIABLE DROPDOWN */
        $.FroalaEditor.DefineIconTemplate("cursorIcon", "<i class=\"fa fa-i-cursor\" aria-hidden=\"true\"></i>\n");
        $.FroalaEditor.DefineIcon("insertingVarList", { NAME: "INSERT", template: "cursorIcon" });
        $.FroalaEditor.RegisterCommand("insertingVarList", {
            title: "Insert Variable",
            type: "dropdown",
            focus: false,
            undo: false,
            refreshAfterCallback: true,

            options: { },

            callback: function (cmd, val) {
                console.log(val);
                
                var cvar = getVar(val, this.opts.defaultVars);

                if (cvar.type == "DICT") {
                    if (ExistDict(cvar.len, this.opts.defaultVars)) {
                        if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                            var cv = $('.fr-code-view').find('textarea')[0];
                            var ts = cv.value.substring(0, cv.selectionStart); 
                            var te = cv.value.substring(cv.selectionEnd); 
                            cv.value = ts + '{var_out:'+cvar.name+'}' + te;
                        }
                        else this.html.insert('<input title="' + cvar.len.replace(/\n/g, ", ") + '" data-cmd="out_var" data-name="' + cvar.name + '" data-type="' + cvar.type + '" data-length="' + cvar.len.replace(/\n/g, ", ") + '" value="' + cvar.view + '" class="' + cvar.class + ' froalaTypes" readonly>');
                    }else {
                        if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                            var cv = $('.fr-code-view').find('textarea')[0];
                            var ts = cv.value.substring(0, cv.selectionStart); 
                            var te = cv.value.substring(cv.selectionEnd); 
                            cv.value = ts + '{out:dict_word,'+cvar.name+'}' + te;
                        }
                        else this.html.insert('<input title="' + cvar.len.replace(/\n/g, ", ") + '" data-cmd="only_out" data-name="' + cvar.name + '" data-type="' + cvar.type + '" data-length="' + cvar.len.replace(/\n/g, ", ") + '" value="' + cvar.view + '" class="' + cvar.class + ' froalaTypes" readonly>');
                    }
                }
                else {
                    if (typeof this.codeView != 'undefined' && this.codeView.isActive()) {
                            var cv = $('.fr-code-view').find('textarea')[0];
                            var ts = cv.value.substring(0, cv.selectionStart); 
                            var te = cv.value.substring(cv.selectionEnd); 
                            cv.value = ts + '{var_out:'+cvar.name+'}' + te;
                    }
                    else this.html.insert('<input title="' + cvar.len.replace(/\n/g, ", ") + '" data-cmd="out_var" data-name="' + cvar.name + '" data-type="' + cvar.type + '" data-length="' + cvar.len.replace(/\n/g, ", ") + '" value="' + cvar.view + '" class="' + cvar.class + ' froalaTypes" readonly>');
                }

                checkSize();
            },

            refresh: function () {
                this.opts.defaultVars = getAllVars(this.html.get(), this.opts.globalVars, this.opts.dictionaries);
                generateTextFromConfig(this.html.get(), this);
                //$(".fr-box.fr-basic.fr-top").find("a:contains('Froala Editor')").parent().remove();
            },

            refreshOnShow: function ($btn, $dropdown) {
                var editorInstance = this,
                    list = $dropdown.find('ul.fr-dropdown-list'),
                    listItems = '',
                    variables = editorInstance.opts.defaultVars;

                variables.forEach(function (v, i) {
                    var type = v.type;
                    if (v.type == "DICT" && ExistDict(v.len, editorInstance.opts.defaultVars))
                        v.type = "VAR_DICT";

                    listItems += '<li><a class="fr-command" data-cmd="insertingVarList" data-param1="' + v.name + '" data-param2="' + v.type +
                        '" data-param3="' + v.len + '" title="' + v.name + '">[' + v.type + "] " + v.name + '</a></li>';
                });

                list.empty().append(listItems);

                if (!editorInstance.selection.inEditor())
                {
                    editorInstance.selection.setAtEnd(editorInstance.$el.get(0));
                    editorInstance.selection.restore();
                }
            }
        });

        /* SUPPORT FUNCS */
        function supportFromDeleteDictionary(name, dicts)
        {
            for (var i = 0; i < dicts.length; i++)
            {
                if (dicts[i].name == name)
                    return i;
            }
            return -1;
        }
        function checkSize() {

            $.each($("input.froalaTypes"), function (i, v) {
                autosizeInput(v);
            })
        }
        function checkName(name, mass) {
            for (var i = 0; i < mass.length; i++)
                if (mass[i].name == name) return true;

            return false;
        }
        function ExistDict(name, varmass)
        {
            for (var i = 0; i < varmass.length; i++)
            {
                if (varmass[i].type == "DICT" && varmass[i].name == name)
                    return true;
            }

            return false;
        }
        function getVar(name, mass) {
            for (var i = 0; i < mass.length; i++)
                if (mass[i].name == name) {
                    var cview = "";
                    if (mass[i].type == "NUM")
                        cview = "NOUT: {" + mass[i].name + "}";
                    if (mass[i].type == "STR")
                        cview = "SOUT: {" + mass[i].name + "}";
                    if (mass[i].type == "DICT")
                        cview = "DOUT: {" + mass[i].name + "}";
                    if (mass[i].type == "VAR_DICT")
                        cview = "VARDICT_OUT: {" + mass[i].name + "}";
                    return {
                        name: mass[i].name,
                        view: cview,
                        len: mass[i].len,
                        type: mass[i].type,
                        class: mass[i].type == "NUM" ? "froalaBeautyNumber" : (mass[i].type == "STR" ? "froalaBeautyString" : "froalaBeautyDictionary")
                    }
                }

            return {};
        }
        function getAllVars(inst, def, dects) {
            var result = [];
            $.each(def, function(i, v) {
                result.push({
                    name: v.name,
                    len: v.value,
                    type: v.type == "numbers" ? "NUM" : (v.type == "letters" ? "STR" : "DICT")
                })
            });
            for (var i = 0; i < dects.length; i++) result.push({
                name: dects[i].name,
                len: dects[i].value,
                type: "DICT"
            });
            var maybeVars = $(inst).find("input.froalaTypes");
            for (var i = 0; i < maybeVars.length; i++)
            {
                if ($(maybeVars[i]).attr("data-name"))
                {
                    if (!checkName($(maybeVars[i]).attr("data-name"), result))
                    {
                        result.push({
                            name: $(maybeVars[i]).attr("data-name"),
                            type: $(maybeVars[i]).attr("data-type"),
                            len: $(maybeVars[i]).attr("data-length")
                        });
                    }
                }
            }
            //console.log(result)
            return result;
        }
        function Exec(str, reg) {
            return str.match(reg)[0];
        }
        function generateConfigFromText(text, obj, inst) {
            inst.opts.textareaObj = obj;

            var res = text;
            
            var attrMacros = [];

            res = text.replace(/ (?=[^<>]*(<|$))/mg, '&nbsp;')

            var currentBreak = text.match(/<br>/g);

            if (currentBreak)
                for (var i = 0; i < currentBreak.length; i++)
                {
                    res = res.replace("<br>", "</p><p>");
                }

            inst.html.set(res);

            var currentMatches = text.match(/\{var\:.+?\=.+?\,.+?\}/g);

            var listMacros = {};
            $.each(currentMatches, function(i,v) {
                if (res.search("'"+v+"'") >= 0 || res.search('"'+v+'"') >= 0) {
                    attrMacros.push({s:v.replace('{','<<<<<<').replace('{','>>>>>>'), r:v});
                    res = res.replace(v, v.replace('{','<<<<<<').replace('{','>>>>>>'));
                }
                else {
                    listMacros[Exec(v, /\{var\:.+?\=/g).replace("{var:", "").replace("=", "")] = {
                        "name": Exec(v, /\{var\:.+?\=/g).replace("{var:", "").replace("=", ""),
                        "type": Exec(v, /\=.+?\,/g).replace("=", "").replace(",", ""),
                        "value": Exec(v, /\,.+?\}/g).replace(",", "").replace("}", "")
                    }
                }
            });

            $.each(listMacros, function (i,v) {
                var type, pref, eclass, regToRep;
                switch (v.type){
                    case "numbers":
                        type = "NUM";
                        pref = "N";
                        eclass = "froalaBeautyNumber";
                        break;
                    case "letters":
                        type = "STR";
                        pref = "S";
                        eclass = "froalaBeautyString";
                        break;
                    default:
                        type = "DICT";
                        pref = "D";
                        eclass = "froalaBeautyDictionary";
                        break;
                }
                var el = '<input title="' + v.value + '" data-cmd="new_var" data-name="' + v.name + '" data-type="' + type + '" data-length="' + v.value + '" value="' + pref + ': {' + v.name + '}" class="' + eclass + ' froalaTypes" readonly>';

                regToRep = "{var:" + v.name + "=" + v.type + "," + v.value + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });
            $.each(attrMacros, function (i,m) {
                res = res.replace(m.s, m.r)
            })
            inst.html.set(res);
            checkSize();

            inst.opts.defaultVars = getAllVars(inst.html.get(), inst.opts.globalVars, inst.opts.dictionaries);

            currentMatches = text.match(/\{var_out\:.+?\}/g);

            listMacros = {};
            attrMacros = [];
            $.each(currentMatches, function(i,v) {
                if (res.search("'"+v+"'") >= 0 || res.search('"'+v+'"') >= 0) {
                    attrMacros.push({s:v.replace('{','<<<<<<').replace('{','>>>>>>'), r:v});
                    res = res.replace(v, v.replace('{','<<<<<<').replace('{','>>>>>>'));
                }
                else {
                    listMacros[Exec(v, /\{var_out\:.+?\}/g).replace("{var_out:", "").replace("}", "")] = {
                        "name": Exec(v, /\{var_out\:.+?\}/g).replace("{var_out:", "").replace("}", "")
                    }
                }
            });

            $.each(listMacros, function (i,v) {
                var regToRep;

                var cvar = getVar(v.name, inst.opts.defaultVars);

                if (ExistDict(cvar.len, inst.opts.defaultVars))
                {
                    cvar.view = "VARDICT_OUT: {" + cvar.name + "}";
                }

                var el = '<input title="' + v.value + '" data-cmd="out_var" data-name="' + cvar.name + '" data-type="' + cvar.type + '" data-length="' + cvar.len+ '" value="' + cvar.view + '" class="' + cvar.class + ' froalaTypes" readonly>';

                regToRep = "{var_out:" + v.name + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });
            $.each(attrMacros, function (i,m) {
                res = res.replace(m.s, m.r)
            })
            inst.html.set(res);
            checkSize();

            currentMatches = text.match(/\{out\:.+?\,.+?\}/g);

            attrMacros = [];

            listMacros = {};
            $.each(currentMatches, function(i,v) {
                if (res.search("'"+v+"'") >= 0 || res.search('"'+v+'"') >= 0) {
                    attrMacros.push({s:v.replace('{','<<<<<<').replace('{','>>>>>>'), r:v});
                    res = res.replace(v, v.replace('{','<<<<<<').replace('{','>>>>>>'));
                }
                else {
                    listMacros[i] = {
                        "type": Exec(v, /\{out\:.+?\,/g).replace("{out:", "").replace(",", ""),
                        "val": Exec(v, /\,.+?\}/g).replace(",", "").replace("}", "")
                    }
                }
            });

            //console.log(listMacros)

            $.each(listMacros, function (i,v) {
                var regToRep, type, eclass, pref;
                switch (v.type){
                    case "numbers":
                        type = "NUM";
                        pref = "NOUT";
                        eclass = "froalaBeautyNumber";
                        break;
                    case "letters":
                        type = "STR";
                        pref = "SOUT";
                        eclass = "froalaBeautyString"
                        break;
                    default:
                        type = "DICT";
                        pref = "DOUT";
                        eclass = "froalaBeautyDictionary";
                        break;
                }

                var cvar = getVar(v.val, inst.opts.defaultVars);
                var el = '<input title="' + (v.val ? v.val.replace(/\n/g, ", ") : cvar.len.replace(/\n/g, ", ")) + '" data-cmd="only_out" data-type="' + type + '" data-length="' + v.val.replace(/\n/g, ",") + '" value="' + pref + ': {' + v.val + '}" class="' + eclass + ' froalaTypes" readonly>';
                regToRep = "{out:" + v.type + "," + v.val + "}";
                regToRep = regToRep.replace(/\s/g, "&nbsp;");
                res = res.replace(new RegExp(regToRep, "g"), el);
            });
            $.each(attrMacros, function (i,m) {
                res = res.replace(m.s, m.r)
            })
            inst.html.set(res);
            inst.html.set(inst.html.get().replace(/\<p\>\<br\>\<\/p\>/g, ""));
            checkSize();
        }
        function generateTextFromConfig(text, inst) {


            var inputs2 = text.match(/{out:dict_word,.+?}/g);
            if (inputs2) {
                for (var i = 0; i < inputs2.length; i ++)
                {
                    allDicts.push(getDictsID(inputs2[i].substr(15, inputs2[i].length-16), inst));
                    allDicts = unique(allDicts);
                }
            }

            var inputs3 = text.match(/{var:.+?=dict_word,.+?}/g);
            if (inputs3) {
                for (var i = 0; i < inputs3.length; i ++)
                {
                    allDicts.push(getDictsID(inputs3[i].substring(5, inputs3[i].search('=dict_word,')), inst));
                    allDicts.push(getDictsID(inputs3[i].substring(inputs3[i].search('=dict_word,')+11, inputs3[i].length - 1), inst));
                    allDicts = unique(allDicts);
                }
            }

            var inputs = text.match(/<input.+?froalaTypes\".+?>/g);
            

            if (inputs)
                for (var i = 0; i < inputs.length; i ++)
                {
                    var name = inputs[i].match(/data-name=\".+?\"/g),
                        type = inputs[i].match(/data-type=\".+?\"/g),
                        length = inputs[i].match(/data-length=\".+?\"/g),
                        command = inputs[i].match(/data-cmd=\".+?\"/g);

                    if (name)
                        name = name[0].replace("data-name=\"", "").replace("\"", "");

                    if (length)
                        length = length[0].replace("data-length=\"", "").replace("\"", "");

                    if (type)
                        type = type[0].replace("data-type=\"", "").replace("\"", "");

                    switch (type){
                        case "NUM":
                            type = "numbers";
                            break;

                        case "STR":
                            type = "letters";
                            break;

                        default:
                            type = "dict_word";
                            allDicts.push(getDictsID(name && !getDictsID(name ? name : length, inst) ? name : length, inst));
                            allDicts = unique(allDicts);
                            break;
                    }

                    if (command)
                        command = command[0].replace("data-cmd=\"", "").replace("\"", "");

                    var temp_res = "";

                    switch(command)
                    {
                        case "new_var":
                            temp_res = "{var:" + name + "=" + type + "," + length + "}";
                            break;

                        case "out_var":
                            temp_res = "{var_out:" + name + "}";
                            break;

                        case "only_out":
                            if (type == "dict_word" && name) length = name;
                            temp_res = "{out:" + type + "," +  length + "}";
                            break;
                    }

                    text = text.replace(inputs[i], temp_res);
                }

            var breaks = text.match(/<\/p><p>/g);
            if (breaks)
                for (var i = 0; i < breaks.length; i++)
                {
                    text = text.replace(breaks[i], "<br>");
                }

            text = text.replace(/<p>/g, "").replace(/<p style=\"\">/g, "").replace(/<\/p>/g, "");
            text = text.replace(/&nbsp;/g, " ");
            $(inst.opts.textareaObj).val(text);
        }
        function getDictsID(name, inst) {
            for (var i = 0; i < inst.opts.dictionaries.length; i++)
            {
                if (inst.opts.dictionaries[i].name == name)
                    return inst.opts.dictionaries[i].id;
            }
            return -1;
        }
        function convertGlobalMacrosToArrau(str) {
            var currentMatches = str.match(/\{var\:.+?\=.+?\,.+?\}/g);

            var listMacros = {};
            $.each(currentMatches, function(i,v) {
                listMacros[Exec(v, /\{var\:.+?\=/g).replace("{var:", "").replace("=", "")] = {
                    "name": Exec(v, /\{var\:.+?\=/g).replace("{var:", "").replace("=", ""),
                    "type": Exec(v, /\=.+?\,/g).replace("=", "").replace(",", ""),
                    "value": Exec(v, /\,.+?\}/g).replace(",", "").replace("}", "")
                }
            });

            return listMacros;
        }
        var unique = function(origArr) {
            var newArr = [],
                origLen = origArr.length,
                found, x, y;

            for (x = 0; x < origLen; x++) {
                found = undefined;
                for (y = 0; y < newArr.length; y++) {
                    if (origArr[x] === newArr[y]) {
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    newArr.push(origArr[x]);
                }
            }
            return newArr;
        }

        $('.source_path_macEditor, .message_content_macEditor, .message_subject_macEditor').on("froalaEditor.initialized", function (e, editor) {
            editor.opts.defaultVars = getAllVars(editor.html.get(), editor.opts.globalVars, editor.opts.dictionaries);
            generateConfigFromText(editor.html.get(), this, editor);
            $.ajax({
                method: "GET",
                url: "/api/macros/get/",

                success: function (res) {
                    editor.opts.dictionaries = res;

                    publicDicts = res;
                },

                error: function() {
                    alert("Cannot load dictionaries");
                }
            });
            $.ajax({
                method: "GET",
                url: "/api/macros/global/",

                success: function (res) {
                    editor.opts.globalVars = convertGlobalMacrosToArrau(res);
                },

                error: function() {
                    alert("Cannot load dictionaries");
                }
            });

            generateTextFromConfig(editor.html.get(), editor);
        })
            .on("froalaEditor.focus", function (e, editor) {
                generateTextFromConfig(editor.html.get(), editor);
            })

        ztoolbarButtons = ["showNumPopupMacros", "|", "showStrPopupMacros", "|", "showDictPopupMacros", "|", "DictPopupMacros", "|", "insertingVarList","-",'fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color', 'lineHeight', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', '-', 'insertLink', 'insertImage', 'insertVideo', 'embedly', 'insertTable', '|', 'specialCharacters', 'insertHR', 'selectAll', 'clearFormatting', '|', 'html', '|', 'undo', 'redo']

        $('.message_content_macEditor').froalaEditor({
            toolbarButtons: ztoolbarButtons,
            toolbarButtonsMD: ztoolbarButtons,
            toolbarButtonsSM: ztoolbarButtons,
            toolbarButtonsXS: ztoolbarButtons,
            toolbarVisibleWithoutSelection: true,
            pluginsEnabled: ["macrosCreateNumPlugin", "macrosCreateStrPlugin", "macrosCreateDictPlugin","codeView","align","color","embedly","fontFamily","fontSize","formatOL","fullscreen","inlineStyle","inlineClass","insertImage","insertLink" ,"insertTable","insertVideo","paragraphFormat","paragraphStyle","quote","specialCharacters"],
            codeViewKeepActiveButtons: ["showNumPopupMacros", "showStrPopupMacros", "showDictPopupMacros", "DictPopupMacros", "insertingVarList"],
            height: 400
        });

        $('.message_content_macEditor').on('froalaEditor.commands.before', function (e, editor, cmd, param1, param2) {
            if (cmd == 'html') {
                if (!editor.codeView.isActive()) {
                    editor.opts.defaultVars = getAllVars(editor.html.get(), editor.opts.globalVars, editor.opts.dictionaries);
                    generateConfigFromText(editor.html.get(), this, editor);
                    generateTextFromConfig(editor.html.get(), editor);
                    editor.html.set($(editor.opts.textareaObj).val())
                }
                else {
                    editor.opts.defaultVars = getAllVars(editor.html.get(), editor.opts.globalVars, editor.opts.dictionaries);
                    generateConfigFromText(editor.codeView.get(),  this, editor);
                }
                
            }
        });

        $('.message_content_macEditor').on('froalaEditor.commands.after', function (e, editor, cmd, param1, param2) {
            if (cmd == 'html') {
                checkSize()
            }
        });


        $('.source_path_macEditor, .message_subject_macEditor').froalaEditor({
            toolbarButtons : ["showNumPopupMacros", "|", "showStrPopupMacros", "|", "showDictPopupMacros", "|", "DictPopupMacros", "|", "insertingVarList", "|"],
            pluginsEnabled: ["macrosCreateNumPlugin", "macrosCreateStrPlugin", "macrosCreateDictPlugin"]
        });

        $("form").unbind();

        $(".fr-box.fr-basic.fr-top").find("a:contains('Froala Editor')").parent().remove();

        $(".radio-group").hide();
        $(".radio-group").css({ "padding": "10px", "background-color": "#3e444c", "border-left": "1px solid #1c1e22", "border-bottom": "1px solid #1c1e22", "border-right": "1px solid #1c1e22" });
        $(".radio-group > input[type=radio]").hide();

        $("#types_radiobtn > li").removeClass("active");
        $("#types_radiobtn > li > a").css("cursor", "pointer");

        if ($(".radio-group:eq(0)").find("input:eq(0)").prop("defaultChecked"))
        {
            $("#types_radiobtn > li:eq(0)").addClass("active");
            $(".radio-group:eq(0)").show();
            $(".radio-group:eq(0)").find("input[type=radio]").prop("checked", true);
        }

        if ($(".radio-group:eq(1)").find("input:eq(0)").prop("defaultChecked"))
        {
            $("#types_radiobtn > li:eq(1)").addClass("active");
            $(".radio-group:eq(1)").show();
            $(".radio-group:eq(1)").find("input[type=radio]").prop("checked", true);
        }

        if ($(".radio-group:eq(2)").find("input:eq(0)").prop("defaultChecked"))
        {
            $("#types_radiobtn > li:eq(2)").addClass("active");
            $(".radio-group:eq(2)").show();
            $(".radio-group:eq(2)").find("input[type=radio]").prop("checked", true);
        }

        $("#types_radiobtn > li").click(function (e) {
            $("#types_radiobtn > li").removeClass("active");
            $(".radio-group").hide();
            $(".radio-group").find("input[type=radio]").prop("checked", false);

            $(".radio-group:eq(" + $(e.target).parent().index() + ")").show();
            $(".radio-group:eq(" + $(e.target).parent().index() + ")").find("input[type=radio]").prop("checked", true);
            $(e.target).parent().addClass("active");
        });

        $("#checkGroupPathAttachments").click(function (e) {
            if (!$("#checkGroupPathAttachments:checked").length) {
                $("#max_path_send, #random_patch_bytes_dec, #randpatchstring, #randpatchenable").attr("disabled", "disabled");
            
            }
            else {
                $("#max_path_send, #random_patch_bytes_dec, #randpatchstring, #randpatchenable").removeAttr("disabled");
            }
        });

        $("input[type='radio']:not(:checked)").parent().find("input:not([type='radio'])").val("");
        $("input[type='radio']:not(:checked)").parent().find("textarea").val("");
        $("input[type='radio']:not(:checked)").parent().find("textarea").text("");
        //$("input[type='radio']:not(:checked)").parent().find(".fr-element.fr-view").html("");
        //$("input[type='radio']:not(:checked)").parent().find("select > option[selected=selected]").removeAttr("selected");
        //$("input[type='radio']:not(:checked)").parent().find(".select2-selection__rendered > li:not(:last-child)").remove();


        if ($("#random_patch_bytes_dec").find(":selected").length > 0)
        {
            $("#checkGroupPathAttachments").attr("checked", "checked");
            $("#checkGroupPathAttachments").prop("checked", true);
            $("#max_path_send, #random_patch_bytes_dec, #randpatchstring, #randpatchenable").removeAttr("disabled");
        } else
        {
            $("#checkGroupPathAttachments").removeAttr("checked", "checked");
            $("#checkGroupPathAttachments").prop("checked", false);
        }

        function formSubmit(e) {
            $(".message_content_macEditor, .source_path_macEditor, .message_subject_macEditor").froalaEditor("events.focus", true);
            $(".message_content_macEditor, .source_path_macEditor, .message_subject_macEditor").froalaEditor("events.focus", true);
            $(".message_content_macEditor, .source_path_macEditor, .message_subject_macEditor").froalaEditor("events.focus", true);

            if ( $('.message_content_macEditor').froalaEditor('codeView.isActive') ) {
                $('.message_content_macEditor').froalaEditor('html.set', $('.message_content_macEditor').froalaEditor('codeView.get'))
            }
            /*if ($(".radio-group:eq(0):visible").length) {
                $("input[name=attach_type]").val("file_path");
            }

            if ($(".radio-group:eq(1):visible").length) {
                $("input[name=attach_type]").val("one_file_from_dir");
            }

            if ($(".radio-group:eq(2):visible").length) {
                $("input[name=attach_type]").val("download_url");
            }*/

            var dicts = unique(allDicts);

            var sel = "<select style='display:none;' name='macros[]' multiple='multiple'>";
            for (var i = 0; i < dicts.length; i++)
            {
                if (dicts[i] && dicts[i] > 0) sel += "<option selected='selected' value='" + dicts[i] + "'></option>"
            }
            sel += "</select>";
            $("form").append(sel);

            if ($(".radio-group:eq(0):visible").length)
            {
                $(".radio-group:eq(1)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(1)").find("select[name='attach_path[one_file_from_dir]']").val("");
                $(".radio-group:eq(1)").find("select[name='random_name[one_file_from_dir][]']").val("");
                $(".radio-group:eq(1)").find("textarea").val("").text("");

                $(".radio-group:eq(2)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(2)").find("input[type=radio]").removeAttr("checked").prop("checked", false);

                if ($("#random_name").parents(".form-group:eq(0)").children("div:eq(0):visible").length)
                {
                    $("#random_name").parents(".form-group:eq(0)").children("div:eq(1)").find("textarea").removeAttr("name");
                }

                if ($("[name='source_files_names_macro[file_path]']").val() != "" && $("#random_name").parents(".form-group:eq(0)").children("div:eq(1):visible").length)
                {
                    $("#random_name").removeAttr("name");
                }
            }

            if ($(".radio-group:eq(1):visible").length)
            {
                $(".radio-group:eq(0)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(0)").find("select[name='attach_path[file_path']").val("");
                $(".radio-group:eq(0)").find("select[name='random_name[file_path][]']").val("");
                $(".radio-group:eq(0)").find("textarea").val("").text("");

                $(".radio-group:eq(2)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(2)").find("input[type=radio]").removeAttr("checked").prop("checked", false);

                if ($("#random_name2").parents(".form-group:eq(0)").children("div:eq(0):visible").length)
                {
                    $("#random_name2").parents(".form-group:eq(0)").children("div:eq(1)").find("textarea").removeAttr("name");
                }

                if ($("[name='source_files_names_macro[one_file_from_dir]']").val() != "" && $("#random_name2").parents(".form-group:eq(0)").children("div:eq(1):visible").length)
                {
                    $("#random_name2").removeAttr("name");
                }
            }

            if ($(".radio-group:eq(2):visible").length)
            {
                $(".radio-group:eq(0)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(0)").find("select[name='attach_path[file_path']").val("");
                $(".radio-group:eq(0)").find("select[name='random_name[file_path][]']").val("");
                $(".radio-group:eq(0)").find("textarea").val("").text("");

                $(".radio-group:eq(1)").find("input:not([type=radio])").val("");
                $(".radio-group:eq(1)").find("select[name='attach_path[one_file_from_dir]']").val("");
                $(".radio-group:eq(1)").find("select[name='random_name[one_file_from_dir][]']").val("");
                $(".radio-group:eq(1)").find("textarea").val("").text("");
            }
            changeAttachment();
        }

        $("[name='is_compress_to_zip[one_file_from_dir]']").change(function () {
            if ($(this).prop("checked")){
                $('.one_file_from_dir_random_names > label > i').show();
                compzipEnabled = true;
            }
            else
            {
                $('.one_file_from_dir_editor').hide();
                $('.one_file_from_dir_random_names').show();
                $('.one_file_from_dir_random_names > label > i').hide();
                compzipEnabled = false;
            }
        })


        $('#loadRandomNames1').on({
            'dragover dragenter dragleave': function(e) {
                e.preventDefault();
                e.stopPropagation();
            },
            'drop': function(e) {
                try {
                    var reader = new FileReader();

                    reader.onload = function (res) {
                        var lines = res.currentTarget.result.split("\n");
                        if (lines[0].length > 100 || lines.length > 100000) return;

                        for (var i = 0; i < lines.length; i++)
                        {
                            if (lines[i] == "") continue;
                            console.log(lines[i]);
                            $("#random_name").append("<option selected value='" + lines[i] + "'>" + lines[i] + "</option>")
                        }
                        $("#random_name").trigger("select2.change");
                    };

                    reader.readAsText(e.originalEvent.dataTransfer.files[0]);
                }catch (e) {
                    console.log(e)
                    alert("Error");
                }
                e.preventDefault();
                return false;
            }
        });

        $('#loadRandomNames2').on({
            'dragover dragenter dragleave': function(e) {
                e.preventDefault();
                e.stopPropagation();
            },
            'drop': function(e) {
                try {
                    var reader = new FileReader();

                    reader.onload = function (res) {
                        var lines = res.currentTarget.result.split("\n");
                        if (lines[0].length > 100 || lines.length > 100000) return;

                        for (var i = 0; i < lines.length; i++)
                        {
                            if (lines[i] == "") continue;
                            console.log(lines[i]);
                            $("#random_name2").append("<option selected value='" + lines[i] + "'>" + lines[i] + "</option>")
                        }
                        $("#random_name2").trigger("select2.change");
                    };
                    reader.readAsText(e.originalEvent.dataTransfer.files[0]);
                }catch (e) {
                    console.log(e)
                    alert("Error");
                }
                e.preventDefault();
                return false;
            }
        });

        $(document).on("click", ".froalaBeautyDictionary", function(e) {
            var data = $(e.currentTarget).attr("data-length");

            console.log(data);

            if (data.indexOf(",") !== -1)
            {
                data = data.split(",")[0];
            }

            alert(publicDicts.find(item => item.name == data).value);
        })

        function changeAttachment() {

            // disable
            if (!$("#checkAttachments").prop("checked"))
            {
                // download url
                $("[name='attach_path[download_url]']").attr('disabled', 'disabled');
                $("[name='name[download_url]']").attr('disabled', 'disabled');

                // one file from dir
                $("[name='name[one_file_from_dir]']").attr('disabled', 'disabled');
                $('#random_name2').select2('destroy');
                $('#random_name2').prop('disabled', true);
                $('#random_name2').select2();
                $('[name="source_files_names_macro[one_file_from_dir]"]').froalaEditor('edit.off');
                $('[name="is_compress_to_zip[one_file_from_dir]"]').attr('disabled', 'disabled');
                $('[name="attach_path[one_file_from_dir]"]').select2('destroy');
                $('[name="attach_path[one_file_from_dir]"]').prop('disabled', true);
                $('[name="attach_path[one_file_from_dir]"]').select2();

                // file path
                $("[name='name[file_path]']").attr('disabled', 'disabled');
                $('#random_name').select2('destroy');
                $('#random_name').prop('disabled', true);
                $('#random_name').select2();
                $('[name="source_files_names_macro[file_path]"]').froalaEditor('edit.off');
                $('[name="is_compress_to_zip[file_path]"]').attr('disabled', 'disabled');
                $('[name="attach_path[file_path]"]').select2('destroy');
                $('[name="attach_path[file_path]"]').prop('disabled', true);
                $('[name="attach_path[file_path]"]').select2();

                // type
                $("[name=attach_type]:eq(0)").prop('checked', true);
            } else {
                // download url
                $("[name='attach_path[download_url]']").removeAttr('disabled');
                $("[name='name[download_url]']").removeAttr('disabled');

                // one file from dir
                $("[name='name[one_file_from_dir]']").removeAttr('disabled');
                $('#random_name2').select2('destroy');
                $('#random_name2').prop('disabled', false);
                $('#random_name2').select2();
                $('[name="source_files_names_macro[one_file_from_dir]"]').froalaEditor('edit.on');
                $('[name="is_compress_to_zip[one_file_from_dir]"]').removeAttr('disabled');
                $('[name="attach_path[one_file_from_dir]"]').select2('destroy');
                $('[name="attach_path[one_file_from_dir]"]').prop('disabled', false);
                $('[name="attach_path[one_file_from_dir]"]').select2();

                // file path
                $("[name='name[file_path]']").removeAttr('disabled');
                $('#random_name').select2('destroy');
                $('#random_name').prop('disabled', false);
                $('#random_name').select2();
                $('[name="source_files_names_macro[file_path]"]').froalaEditor('edit.on');
                $('[name="is_compress_to_zip[file_path]"]').removeAttr('disabled');
                $('[name="attach_path[file_path]"]').select2('destroy');
                $('[name="attach_path[file_path]"]').prop('disabled', false);
                $('[name="attach_path[file_path]"]').select2();

                // type
                switch($("#types_radiobtn > li.active").index()) {
                    case 0:
                        $("[name=attach_type]:eq(1)").prop('checked', true);
                        break;
                    case 1:
                        $("[name=attach_type]:eq(2)").prop('checked', true);
                        break;
                    case 2:
                        $("[name=attach_type]:eq(3)").prop('checked', true);
                        break;
                }
            }
        }

        var t1, t2, c1 = 0, c2 = 0;

        app.controller('proxies', function ($scope, $http) {
            $scope.proxies = [];

            angular.element(document).ready(function () {
                $http.get('/api/emails/proxy/check/').then(function (res) {
                    $scope.proxies = res.data;
                })
            })
        })

        app.controller('emails', function($scope, $http){
            angular.element(document).ready(function () {

                var rn = $('#random_name').select2({
                    placeholder: "You can drag'n'drop here a dictionary file (one word per line, with .txt extension)",
                    tags: true,
                    allowClear: true,
                    tokenSeparators: [',', ' ']
                }).data('select2');
                rn.$container.css('padding', 0);
                $(rn.$container).find("input").css("width", "500px")

                var rn = $('#random_name2').select2({
                    placeholder: "You can drag'n'drop here a dictionary file (one word per line, with .txt extension)",
                    tags: true,
                    allowClear: true,
                    tokenSeparators: [',', ' ']
                }).data('select2');
                rn.$container.css('padding', 0);
                $(rn.$container).find("input").css("width", "500px")

                $('#random_patch_bytes_dec').select2({
                    placeholder: "Random patch bytes dec..",
                    tags: true,
                    allowClear: true,
                    tokenSeparators: [',', ' ']
                }).data('select2').$container.addClass("input-sm").css('padding', 0);

                $(".attach_path").select2({
                    minimumResultsForSearch: -1,
                    allowClear: true,
                });

                $scope.openpage = function (page) {
                    window.open(page, '');
                }

                if ($(".radio-group:eq(0)").find("ul.select2-selection__rendered > li").length == 1)
                {
                    t1 = setInterval(function() {
                        if (c1 > 500) clearInterval(t1);
                        else c1++;
                        $(".radio-group:eq(0)").find("ul.select2-selection__rendered").parents(".form-group:eq(0)").find(" > div:eq(1)").attr("style", "display: block;");
                        $(".radio-group:eq(0)").find("ul.select2-selection__rendered").parents(".form-group:eq(0)").find(" > div:eq(0)").attr("style", "display: none;");

                        if (!$("[name='is_compress_to_zip[one_file_from_dir]']:checked").length)
                        {
                            $('.one_file_from_dir_editor').hide();
                            $('.one_file_from_dir_random_names').show();
                            $('.one_file_from_dir_random_names > label > i').hide();
                            compzipEnabled = false;
                        }

                        $("#random_name").trigger("select2.change");
                    }, 100);
                }

                if ($(".radio-group:eq(1)").find("ul.select2-selection__rendered > li").length == 1)
                {
                    t2 = setInterval(function() {
                        if (c2 > 500) clearInterval(t2);
                        else c2++;
                        $(".radio-group:eq(1)").find("ul.select2-selection__rendered").parents(".form-group:eq(0)").find(" > div:eq(1)").attr("style", "display: block;");
                        $(".radio-group:eq(1)").find("ul.select2-selection__rendered").parents(".form-group:eq(0)").find(" > div:eq(0)").attr("style", "display: none;");

                        if (!$("[name='is_compress_to_zip[one_file_from_dir]']:checked").length)
                        {
                            $('.one_file_from_dir_editor').hide();
                            $('.one_file_from_dir_random_names').show();
                            $('.one_file_from_dir_random_names > label > i').hide();
                            compzipEnabled = false;
                        }

                        $("#random_name2").trigger("select2.change");
                    }, 100);
                }

                changeAttachment();
            });
        });
    </script>
@endsection