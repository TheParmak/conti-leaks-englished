@extends('layouts.app')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/froala_editor.pkgd.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/froala_style.min.css') }}">
    <script type="text/javascript" src="{{ asset('/js/froala_editor.pkgd.min.js') }}"></script>



    <div class="container" ng-controller="EmailsCtrl" ng-init="init()">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Create email</div>

                    <div class="panel-body">
                        {{ Form::open() }}
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6" style="text-align: center;">
                                            {{ Form::radio('type', 0, !isset($email->type) || $email->type == 0, ['id' => 'type_1', 'ng-model' => 'type']) }} <label for="type_1">Outlook</label>
                                        </div>
                                        <div class="col-md-6" style="text-align: center;">
                                            {{ Form::radio('type', 1, $email->type == 1, ['id' => 'type_2', 'ng-model' => 'type']) }} <label for="type_2">Thunderbird agent</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::textarea('title', $email->title,  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Title',
                                        'id' => 'subject'
                                    ]) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::text('from', $email->from,  [
                                        'class' => 'form-control',
                                        'placeholder' => 'From',
                                        'required' => '',
                                        'onkeyup' => 'changeFrom(event)',
                                        'id' => 'subtitle'
                                    ]) }}
                                </td>
                            </tr>
                            <tr ng-show="selectedType() == 0">
                                <td>
                                    {{--<textarea name="body" id="myEditor" placeholder="Body">{!! $email->body !!}</textarea>
                                    <textarea name="simple_body" id="simple_body" style="display: none;"></textarea>--}}

                                    {{ Form::textarea('body_outlook', empty($email->type) || $email->type == 0 ? $email->body : '',  [
                                        'class' => 'form-control',
                                        'placeholder' => 'HTML',
                                        'id' => 'subject',
                                        'rows' => '6'
                                    ]) }}
                                </td>
                            </tr>
                            <tr ng-show="selectedType() == 0">
                                <td>
                                    {{ Form::textarea('simple_body_outlook', empty($email->type) || $email->type == 0 ? $email->simple_body : '',  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Simple',
                                        'rows' => '6',
                                        'required' => ''
                                    ]) }}
                                </td>
                            </tr>



                            <tr ng-show="selectedType() == 1">
                                <td>
                                    <textarea name="body" id="myEditor" placeholder="Body">{!! !empty($email->type) && $email->type == 1 ?  $email->body : ''!!}</textarea>
                                    <textarea name="simple_body" id="simple_body" style="display: none;"></textarea>
                                </td>
                            </tr>



                            <tr>
                                <td>
                                    <a class='btn btn-info pull-left' type="submit" ng-show="selectedType() == 1" onclick="showRender()" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) });'>
                                        Preview
                                        <span class="glyphicon glyphicons-eye-open"></span>
                                    </a>
                                    {{--<button class='btn btn-success pull-right' type="submit" onmouseover='$("form").unbind();$("form").on("submit", function (e) { formSubmit(e) });' id="submitBtn" disabled>
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </button>--}}

                                    <a class='btn btn-success pull-right' id="submitBtn" ng-click="saveMail()" disabled>
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </a>
                                </td>
                            </tr>
                            <tr style="display: none;" ng-if="selectedType() == 1">
                                <td>
                                    <h3>Render</h3>
                                    <div id="render"></div>
                                </td>
                            </tr>
                        </table>
                        <script>
                            /*$(function() {
                                $(".fr-box.fr-basic.fr-top").find("a:contains('Froala Editor')").parent().remove()
                                $(".fr-iframe").contents().find("link").each(function(i, v) { if ($(v).attr("href").indexOf("font-awesome") != -1) $(".fr-iframe").contents().find("link:eq(" + i + ")").remove() })
                                $(".fr-iframe").attr("style", "height: auto;");
                            });*/
                        </script>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .froalaPopupInput {
            font-size: 13px;
            -webkit-border-radius: 5px !important;
            -moz-border-radius: 5px !important;
            border-radius: 5px !important;
            height: 35px;
            margin: 5px;
            outline: none;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 0 10px;
            width: 95%;
        }

        .froalaBeautyNumber {
            color: white;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            font-size: 13px;
            padding: 0;
            margin: 0;
            background: #2f9dfc;
            border: 1px solid #2f88fc;
            padding: 0 5px;
            text-align: center;
        }

        .froalaBeautyString {
            color: white;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            font-size: 13px;
            padding: 0;
            margin: 0;
            background: #00be7e;
            border: 1px solid #00875c;
            padding: 0 5px;
            text-align: center;
        }

        .froalaBeautyDictionary {
            color: white;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            font-size: 13px;
            padding: 0;
            margin: 0;
            background: #be0049;
            border: 1px solid #8f0036;
            padding: 0 5px;
            text-align: center;
        }

        .custom-layer > label{
            display: none;
        }

        .froala-has-error {
            border: 1px #ff9f35 solid;
        }
    </style>

    <script>
        (function(){var t=/\s/g;var e=/>/g;var n=/</g;function i(i){return i.replace(t,"&nbsp;").replace(e,"&lt;").replace(n,"&gt;")}var r="__autosizeInputGhost";function o(){var t=document.createElement("div");t.id=r;t.style.cssText="display:inline-block;height:0;overflow:hidden;position:absolute;top:0;visibility:hidden;white-space:nowrap;";document.body.appendChild(t);return t}var a=o();function d(t,e){var n=window.getComputedStyle(t);var d="box-sizing:"+n.boxSizing+";border-left:"+n.borderLeftWidth+" solid black"+";border-right:"+n.borderRightWidth+" solid black"+";font-family:"+n.fontFamily+";font-feature-settings:"+n.fontFeatureSettings+";font-kerning:"+n.fontKerning+";font-size:"+n.fontSize+";font-stretch:"+n.fontStretch+";font-style:"+n.fontStyle+";font-variant:"+n.fontVariant+";font-variant-caps:"+n.fontVariantCaps+";font-variant-ligatures:"+n.fontVariantLigatures+";font-variant-numeric:"+n.fontVariantNumeric+";font-weight:"+n.fontWeight+";letter-spacing:"+n.letterSpacing+";margin-left:"+n.marginLeft+";margin-right:"+n.marginRight+";padding-left:"+n.paddingLeft+";padding-right:"+n.paddingRight+";text-indent:"+n.textIndent+";text-transform:"+n.textTransform;function f(e){e=e||t.value||t.getAttribute("placeholder")||"";if(document.getElementById(r)===null){a=o()}a.style.cssText+=d;a.innerHTML=i(e);var n=window.getComputedStyle(a).width;t.style.width=n;return n}t.addEventListener("input",function(){f()});var l=f();if(e&&e.minWidth&&l!=="0px"){t.style.minWidth=l}return f}if(typeof module==="object"){module.exports=d}else{window.autosizeInput=d}})();
    </script>

    <script type="text/javascript">
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
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Length" name="numberLength">' +
                    '</div>'
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
            title: "Define Number Variable",
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

                var len = $("input[name=numberLength]:visible").val();

                $("input[name=numberLength]:visible").removeClass("froala-has-error");

                if (len == null || len == 0 || len.match(/^[0-9]+$/g) == null)
                {
                    $("input[name=numberLength]:visible").addClass("froala-has-error");
                    return;
                }

                var res = '<input data-cmd="only_out" class="froalaTypes froalaBeautyNumber" readonly title="' + len + '" data-type="DIG" data-length="' + len + '" value="DIGIT: {' + len + '}">';

                $("input[name=numberLength]:visible").val("");

                this.macrosCreateNumPlugin.hidePopup();
                this.html.insert(res);

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
                    '<input type="text" class="froalaPopupInput" placeholder="Variable Length" name="stringLength">' +
                    '</div>'
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
            title: "Define String Variable",
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

                var len = $("input[name=stringLength]:visible").val();

                $("input[name=stringLength]:visible").removeClass("froala-has-error");

                if (len == null || len == 0 || len.match(/^[0-9]+$/g) == null)
                {
                    $("input[name=stringLength]:visible").addClass("froala-has-error");
                    return;
                }

                var res = '<input data-cmd="only_out" class="froalaTypes froalaBeautyString" readonly title="' + len + '" data-type="LET" data-length="' + len + '" value="LETTER: {' + len + '}">';

                $("input[name=stringLength]:visible").val("");

                this.macrosCreateStrPlugin.hidePopup();
                this.html.insert(res);

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
                    '<input type="text" class="froalaPopupInput" placeholder="Words (split by , without spaces)" name="dictWords">' +
                    '</div>'
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

                var words = $("input[name=dictWords]:visible").val();

                $("input[name=dictWords]:visible").removeClass("froala-has-error");

                if (words == "" || words.length == 0)
                {
                    $("input[name=dictWords]:visible").addClass("froala-has-error");
                    return;
                }

                var res = '<input data-cmd="only_out" class="froalaTypes froalaBeautyDictionary" readonly title="' + words + '" data-type="DICT" data-length="' + words.split(',').length + '" value="DICT: {' + words.split(',')[0] + '}">';

                $("input[name=dictWords]:visible").val("");

                this.macrosCreateDictPlugin.hidePopup();
                this.html.insert(res);

                checkSize();
            }
        });

        /* END DICTIONARY IN VAR*/

        /* SUPPORT FUNCS */
        function checkSize() {

            $.each($("input.froalaTypes"), function (i, v) {
                autosizeInput(v);
            })
        }
        function generateConfigFromText(text, obj, inst) {
            var res = text;

            //res = res.replace(/\s/g, "&nbsp;");

            /*var currentBreak = text.match(/<br>/g);

            if (currentBreak)
                for (var i = 0; i < currentBreak.length; i++)
                {
                    res = res.replace("<br>", "</p><p>");
                }

            inst.html.set(res);*/

            var skipAttrs = res;

            skipAttrs = skipAttrs.replace(/src="{(?!digit|letter).+?}"/g, "");
            skipAttrs = skipAttrs.replace(/href="{(?!digit|letter).+?}"/g, "");

            var currentMatches = skipAttrs.match(/{(?!digit|letter).+?}/g);
            var listMacros = [];

            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{.+?}/g)[0].replace("{", "").replace("}", "")
                })
            });

            $.each(listMacros, function (i,v) {
                let el = '<input data-cmd="only_out" class="froalaTypes froalaBeautyDictionary" readonly title="' + v.len + '" data-type="DICT" data-length="' + v.len.split(',').length + '" value="DICT: {' + v.len.split(',')[0] + '}">';

                let regToRep = "{" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });

            inst.html.set(res);
            checkSize();

            currentMatches = text.match(/\{digit[0-9]+}/g);

            listMacros = [];
            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{digit[0-9]+}/g)[0].replace("{digit", "").replace("}", ""),
                    "type": "digit"
                })
            });

            $.each(listMacros, function (i,v) {
                let el = '<input data-cmd="only_out" class="froalaTypes froalaBeautyNumber" readonly title="' + v.len + '" data-type="DIG" data-length="' + v.len + '" value="DIGIT: {' + v.len + '}">';

                let regToRep = "{digit" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });

            inst.html.set(res);
            checkSize();

            currentMatches = res.match(/\{letter[0-9]+}/g);
            listMacros = [];

            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{letter[0-9]+}/g)[0].replace("{letter", "").replace("}", ""),
                    "type": "letter"
                })
            });

            $.each(listMacros, function (i,v) {
                let el = '<input data-cmd="only_out" class="froalaTypes froalaBeautyString" readonly title="' + v.len + '" data-type="LET" data-length="' + v.len + '" value="LETTER: {' + v.len + '}">';

                let regToRep = "{letter" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });

            res = res.replace(/%3A/g, ":");


            inst.html.set(res);
            //  inst.html.set(inst.html.get().replace(/\<p\>\<br\>\<\/p\>/g, ""));
            checkSize();
        }
        function generateTextFromConfig(text, inst) {
            var inputs = text.match(/<input data-cmd="only_out" class="froalaTypes.+?" readonly.+?>/g);

            if (inputs)
                for (var i = 0; i < inputs.length; i ++)
                {
                    var len = inputs[i].match(/title=\".+?\"/g),
                        type = inputs[i].match(/data-type=\".+?\"/g);

                    if (len)
                        len = len[0].replace("title=\"", "").replace("\"", "");

                    if (type)
                        type = type[0].replace("data-type=\"", "").replace("\"", "");

                    var temp_res = "";

                    switch (type){
                        case "DIG":

                            temp_res = "{digit" + len + "}";
                            break;

                        case "LET":

                            temp_res = "{letter" + len + "}";
                            break;

                        default:
                            temp_res = "{" + len + "}";
                            break;
                    }

                    text = text.replace(inputs[i], temp_res);
                }

            /*var breaks = text.match(/<\/p><p>/g);
            if (breaks)
                for (var i = 0; i < breaks.length; i++)
                {
                    text = text.replace(breaks[i], "<br>");
                }

            text = text.replace(/<p>/g, "").replace(/<p style=\"\">/g, "").replace(/<\/p>/g, "");*/
            text = text.replace(/%3A/g, ":");
            text = text.replace(/&nbsp;/g, " ");

            $(inst).val(text);
            renderMacros(text);
        }

        function random(min, max, type) {

            switch (type) {
                case "word":
                    return random(min, max);
                    break;

                case "digit":
                    var res = "";
                    for (var i = 0; i < max; i++)
                    {
                        res += random(0, 9);
                    }
                    return res;
                    break;

                case "letter":
                    var res = "";
                    var dict = "qwertyuiopasdfghjklzxcvbnm";
                    for (var i = 0; i < max; i++)
                    {
                        res += dict[random(0, dict.length - 1)];
                    }
                    return res;
                    break;

                default:
                    min = Math.ceil(min);
                    max = Math.floor(max + 1);
                    return Math.floor(Math.random() * (max - min)) + min;
            }
        }
        function renderMacros(text) {
            var res = text;

            var currentMatches = res.match(/{(?!digit|letter).+?}/g);
            var listMacros = [];

            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{.+?}/g)[0].replace("{", "").replace("}", "")
                })
            });

            $.each(listMacros, function (i,v) {
                let el = v.len.split(',')[random(0, v.len.split(',').length - 1, "word")];

                let regToRep = "{" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });

            currentMatches = text.match(/\{digit[0-9]+}/g);

            listMacros = [];
            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{digit[0-9]+}/g)[0].replace("{digit", "").replace("}", ""),
                    "type": "digit"
                })
            });

            $.each(listMacros, function (i,v) {
                let el = random(v.len, v.len, "digit");

                let regToRep = "{digit" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });


            currentMatches = res.match(/\{letter[0-9]+}/g);
            listMacros = [];

            $.each(currentMatches, function(i,v) {
                listMacros.push({
                    "len": v.match(/{letter[0-9]+}/g)[0].replace("{letter", "").replace("}", ""),
                    "type": "letter"
                })
            });

            $.each(listMacros, function (i,v) {
                let el = random(v.len, v.len, "letter");

                let regToRep = "{letter" + v.len + "}";

                res = res.replace(new RegExp(regToRep, "g"), el);
            });

            $("#render").html(res);

            /*if ($(text).val() == "")
            {
                $("[name=simple_body]").val(text);
            } else {
                try {
                    let text = $(text).val().replace(/<br>/g, '\n').replace(/<\/p><p>/g, '\n').replace(/<\/p><p.+?>/g, '\n');
                } catch(e) {}
                $("[name=simple_body]").val($(text).html());
            }*/
        }

        function showRender() {
            $("#myEditor").froalaEditor("events.focus", true);

            $("#render").parent().parent().show();
            $("#simple").parent().parent().show();
        }

        $('#subject, #myEditor').on("froalaEditor.initialized", function (e, editor) {
            generateConfigFromText(editor.html.get(), this, editor);

            generateTextFromConfig(editor.html.get(), this);
        })
            .on("froalaEditor.focus", function (e, editor) {
                generateTextFromConfig(editor.html.get(), this, editor);

                if ($($("#myEditor").val()).text() == "")
                    $("#simple_body").val($("#myEditor").val());
                else {
                    let text = $("#myEditor").val().replace(/<br>/g, '\n').replace(/<\/p><p>/g, '\n').replace(/<\/p><p.+?>/g, '\n');
                    $("#simple_body").val($(text).text());
                }

                try {
                    if ($($("#subject").val()).text() == "")
                        $("#subject").val($("#subject").val());
                    else
                        $("#subject").val($($("#subject").val()).text());
                } catch (e) {}
            })

        $('#subject').froalaEditor({
            toolbarButtons : ["showNumPopupMacros", "|", "showStrPopupMacros", "|", "showDictPopupMacros"],
            pluginsEnabled: ["macrosCreateNumPlugin", "macrosCreateStrPlugin", "macrosCreateDictPlugin"]
        });

        $('#myEditor').froalaEditor({
            toolbarButtons : ["showNumPopupMacros", "|", "showStrPopupMacros", "|", "showDictPopupMacros", "|", "DictPopupMacros", "|", "insertingVarList", "|", "bold", "italic", "underline", "|", "align", "formatOL", "formatUL", "|", "html"],
            pluginsEnabled: ["macrosCreateNumPlugin", "macrosCreateStrPlugin", "macrosCreateDictPlugin", "codeView"],
            height: 300
        });

        $("form").unbind();

        $(".fr-box.fr-basic.fr-top").find("a:contains('Froala Editor')").parent().remove();

        function formSubmit() {


        }

        function changeFrom(e) {
            if (!$(e.target).val().length) $('#submitBtn').attr('disabled', 'disabled');
            else $('#submitBtn').removeAttr('disabled');
        }

        app.controller('EmailsCtrl', function ($scope) {
            $scope.type = 0;

            $scope.init = function () {
                console.log($scope.selectedType());
                $scope.type = $('#type_1:checked, #type_2:checked').val();
            };
            $scope.selectedType = function () {
                return $('#type_1:checked, #type_2:checked').val();
            };

            $scope.saveMail = function () {
                $("#subject, #myEditor").froalaEditor("events.focus", true);
                $("#subject, #myEditor").froalaEditor("events.focus", true);
                $("#subject, #myEditor").froalaEditor("events.focus", true);

                if ($scope.type == 1) {
                    if ($('#subject').val().length && $('#subtitle').val().length && $('#myEditor').val().length && $('#simple_body').val().length) {
                        const save_request = {
                            _token: $('[name=_token]').val(),
                            type: $scope.type,
                            title: $('#subject').val(),
                            from: $('#subtitle').val(),
                            body: $('#myEditor').val(),
                            simple_body: $('#simple_body').val()
                        };

                        $.post(location.pathname, save_request, function () {
                            location.href = '/emails';
                        });
                    } else {
                        alert('Fill all fields!');
                    }
                } else {
                    if ($('#subject').val().length && $('#subtitle').val().length && $('[name=body_outlook]').val().length && $('[name=simple_body_outlook]').val().length) {
                        const save_request = {
                            _token: $('[name=_token]').val(),
                            type: $scope.type,
                            title: $('#subject').val(),
                            from: $('#subtitle').val(),
                            body: $('[name=body_outlook]').val(),
                            simple_body: $('[name=simple_body_outlook]').val()
                        };

                        $.post(location.pathname, save_request, function () {
                            location.href = '/emails';
                        });
                    } else {
                        alert('Fill all fields!');
                    }
                }
            }
        })

    </script>
@endsection
