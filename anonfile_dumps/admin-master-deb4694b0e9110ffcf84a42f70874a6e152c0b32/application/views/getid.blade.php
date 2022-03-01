<!DOCTYPE html>
<html lang='ru' xml:lang='ru' xmlns='http://www.w3.org/1999/xhtml'>
<head>
    <title>Title</title>
    <base href="/">
    <meta http-equiv=Content-Type content="text/html;charset=UTF-8">
    <script src="/template/js/angular.min.js"></script>
    <style type="text/css">
        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }
        .results-table { width:100%; }
        #main-sheet { width:90%; margin:0 auto; text-align: center; }
    </style>
</head>

<body ng-app="app">
<div ng-controller="controller" id="main-sheet">
    <h2>@{{ data.id }}</h2>
    <table class="results-table">
        <tr class="table-header">
            <th>id</th>
            <th>name</th>
            <th>Group</th>
            <th>Importance</th>
            <th>created_at</th>
            <th>logged_at</th>
            <th>last_activity</th>
            <th>id_low</th>
            <th>id_high</th>
            <th>ip</th>
            <th>sys_ver</th>
            <th>country</th>
            <th>client_ver</th>
            <th>userdefined</th>
            <th>devhash_1</th>
            <th>devhash_2</th>
            <th>devhash_3</th>
            <th>devhash_4</th>
        </tr>
        <tr ng-repeat="item in data.table1">
            <td>@{{item.id}}</td>
            <td>@{{item.name}}</td>
            <td>@{{item.group}}</td>
            <td>@{{item.importance}}</td>
            <td>@{{item.created_at}}</td>
            <td>@{{item.logged_at}}</td>
            <td>@{{item.last_activity}}</td>
            <td>@{{item.id_low}}</td>
            <td>@{{item.id_high}}</td>
            <td>@{{item.ip}}</td>
            <td>@{{item.sys_ver}}</td>
            <td>@{{item.country}}</td>
            <td>@{{item.client_ver}}</td>
            <td>@{{item.userdefined}}</td>
            <td>@{{item.devhash_1}}</td>
            <td>@{{item.devhash_2}}</td>
            <td>@{{item.devhash_3}}</td>
            <td>@{{item.devhash_4}}</td>
        </tr>
    </table>
    <br /><br />

    <table class="results-table">
        <tr class="table-header">
            <th>Client ID</th>
            <th>Created at</th>
            <th>Type</th>
            <th>Info</th>
            <th>Command</th>
        </tr>
        <tr ng-repeat="item in data.table2">
            <td>@{{ item.client_id }}</td>
            <td>@{{ item.created_at }}</td>
            <td>@{{ item.type }}</td>
            <td>@{{ item.info }}</td>
            <td>@{{ item.command }}</td>
        </tr>
    </table>
    <br /><br />

    <table class="commands-table">
        <tr class="table-header">
            <th>Command name</th>
            <th>Param</th>
        </tr>
        <tr ng-repeat="item in data.table3">
            <td>@{{ item.incode }}</td>
            <td>@{{ item.params }}</td>
        </tr>
    </table>
</div>
<script>
    var app = angular.module('app', []);

    app.controller("controller", function ($scope, $http, $window, $location) {
        var adress = "/apiEx/getID";
        var absurl = $location.absUrl();
        var key = '/';

        lastpos = absurl.lastIndexOf(key);
        var clientID = absurl.substr(lastpos);
        console.log("Client ID is: " + clientID);

        adress += clientID;

        console.log("Adress to request is: " + adress);

//    $scope.data = {
//      id: "234234234",
//      table1: { testkey1: "testvalue2", testkey2: "fsdfsdfsd" },
//      table2: [
//      { client_id: "3424234", created_at: "fsdfsdfsd", type: "none", info: "infoinfo", command:"try" },
//      { client_id: "2342333", created_at: "22222222", type: "tre", info: "ffff", command:"do" }
//    ] };
        angular.element(document).ready(function () {
            $http.get(adress).then(function (response) {
                $scope.data = response.data;
            });
        });

    });
</script>
</body>
</html>
