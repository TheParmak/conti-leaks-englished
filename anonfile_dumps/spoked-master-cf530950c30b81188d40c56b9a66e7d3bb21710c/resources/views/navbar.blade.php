<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">
                <span class="glyphicon glyphicon-home"></span>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger" style="margin-right: 4px;"></span>Manage<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Mailouts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('files_list_index') }}">Files</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger" style="margin-right: 4px;"></span>Configs<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        @if(Auth::user()->name == 'root')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('config_database') }}">Database</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('config_general') }}">General Options</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('config_mail_proxies') }}">Mail Proxies</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('config_global_macros') }}">Global macros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('config_blacklist') }}">BlackList</a>
                        </li>
                        @if(Auth::user()->name == 'root')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('config_web_hosts') }}">Web Hosts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('config_script_types') }}">Script Types</a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="/logout"><span class="glyphicon glyphicon-log-out" style="margin-right: 4px;"></span>Logout ({{ Auth::user()->name }})</a>
                </li>
            </ul>
        </div>
    </div>
</nav>