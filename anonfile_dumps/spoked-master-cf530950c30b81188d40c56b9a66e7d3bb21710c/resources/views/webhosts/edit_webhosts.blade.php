@extends('layouts.app')

@section('content')
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ol class="breadcrumb" style="margin-bottom: 10px;">
                        <li><a href="{{ route("config_web_hosts") }}">Webmail Hosts</a></li>
                        <li class="active">{{ $host->name ? "Edit" : "New" }}</li>
                    </ol>

                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endforeach
                    @endif

                    {{ Form::open() }}
                    <div class="panel panel-default">
                        <div class="panel-heading">Webmail host</div>
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td style="border-top: none;">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="text-align: center;">
                                                Name
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::text('name', old("name") ? old("name") : $host->name,  [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Name'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="text-align: center;">
                                                Cookies
                                            </label>
                                            <div class="col-sm-10">
                                                <table class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <td>Name</td>
                                                        <td>Domain</td>
                                                        <td style="width: 1px;">
                                                            <a class="btn btn-success pull-right btn-xs" onclick="addRowCookie()">
                                                                <span class="glyphicon glyphicon-plus"></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="all_cookies">
                                                    @foreach($cookies as $cookie)
                                                        <tr>
                                                            <td>
                                                                {{ Form::text('cookieName[]', old("name") ? old("name") : $cookie["name"],  [
                                                                    'class' => 'form-control',
                                                                    'placeholder' => 'Name',
                                                                    'required' => ''
                                                                ]) }}
                                                            </td>
                                                            <td>
                                                                {{ Form::text('cookieDomain[]', old("name") ? old("name") : $cookie["domain"],  [
                                                                    'class' => 'form-control',
                                                                    'placeholder' => 'Domain'
                                                                ]) }}
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-danger pull-right" onclick="deleteCookie(event)"><span class="glyphicon glyphicon-trash"></span></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="text-align: center;">
                                                Grab Script
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('grab_script', $grab_script,  [
                                                    'class' => 'form-control macEditor message_content_macEditor'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="text-align: center;">
                                                Send Script
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('send_script', $send_script,  [
                                                    'class' => 'form-control macEditor message_content_macEditor'
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <div class="btn-group pull-right" role="group" aria-label="...">
                                <button class='btn btn-success' type="submit" name="action" value="save">
                                    <span class="glyphicon glyphicon-ok"></span> Save
                                </button>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function addRowCookie() {
            if ($("#all_cookies").find("tr:last > td > input").val() == "")
            {
                console.log(1);
            } else {
                $("#all_cookies").append("<tr><td><input class=\"form-control\" placeholder=\"Name\" name=\"cookieName[]\" type=\"text\" required></td><td><input class=\"form-control\" placeholder=\"Domain\" name=\"cookieDomain[]\" type=\"text\"></td><td><button class=\"btn btn-danger pull-right\" onclick=\"deleteCookie(event)\"><span class=\"glyphicon glyphicon-trash\"></span></button></td></tr>");
            }
        }

        function deleteCookie(e) {
            if (confirm('Are you sure?'))
            {
                e.preventDefault;
                $(e.target).parents("tr:eq(0)").remove();

                return false;
            }

            return false;
        }
    </script>
@endsection
