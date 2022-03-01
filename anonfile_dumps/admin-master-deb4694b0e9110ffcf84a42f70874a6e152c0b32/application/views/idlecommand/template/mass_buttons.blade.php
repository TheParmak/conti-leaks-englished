<div class="btn-group btn-group-sm pull-right">
    <button class="btn btn-inverse btn-warning" ng-disabled = "!multipleAvaible" ng-click = "massStop()">
        <span class="glyphicon glyphicon-stop"></span>
    </button>
    <button class="btn btn-inverse btn-danger" ng-disabled = "!multipleAvaible" ng-click = "massDel()">
        <span class="glyphicon glyphicon-trash"></span>
    </button>
    <a href="/idlecommand/editor/" class="btn btn-success btn-inverse">
        <span class="glyphicon glyphicon-plus"></span>
    </a>
    <button type="button" class="btn btn-danger btn-xs btn-inverse" ng-disabled="!multipleAvaible" ng-click="massRefresh()">
        <span class="glyphicon glyphicon-refresh"></span>
    </button>
    @if(isset($_GET['params_as']))
        <a href="/idlecommand/" class="btn btn-success btn-inverse">
            Reset
        </a>
    @endif
</div>
