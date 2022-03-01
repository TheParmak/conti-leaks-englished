@include('clients.template.client_filter', ['post' => $post])

@include('clients.template.filter', [
    'lastactivity_options' => $lastactivity_options,
    'location_options' => $location_options,
    'have_comment_options' => ['All', 'Without comments', 'With comments' ]
])
<div ng-controller="main">
    @include('clients.template.table', ['post' => $post])
    {{--@include('clients.template.stat.global', ['clients' => $clients_stat, 'post' => $post])--}}

    <div class="modal-bg" style="display: none;" ng-click="closeModalDialog($event)">
          @include('clients.template.modals.comments')
    </div>

</div>

<div ng-controller="stat">
    @include('clients.template.stat.geo', ['post' => $post])
    @include('clients.template.stat.system', ['post' => $post])
    @include('clients.template.stat.user', ['post' => $post])
</div>

@include('clients.template.script')

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart']});

    app.controller('statBtn', function($scope, $rootScope){
        var message = {message: '<h1 style="color:black;">Loading...</h1>'};

        $scope.geoStatBtn = function(){
            $.blockUI(message);
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/geo/',
                'chart': 'chart_div_geo',
                'modal': '#myModalGeo'
            });
        };

        $scope.systemStatBtn = function(){
            $.blockUI(message);
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/system/',
                'chart': 'chart_div_system',
                'modal': '#myModalSystem'
            });
        };

        $scope.userSystemStatBtn = function(){
            $.blockUI(message);
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/usersystem/',
                'chart': 'chart_div_system_user',
                'modal': '#myModalUserSystem'
            });
        };
    });

    app.controller('stat', function($scope, $rootScope, $http){
        $scope.info = [];

        $rootScope.$on("forStat", function (event, args) {
            $http.post(args.url, args.post).then(function (res) {
                console.log("stat post success on clients\index.blade.php");
                if(res.data.detailedStat != undefined) { $scope.info = res.data.detailedStat; }
                $scope.drawChart(res.data, args.chart);
            }, function (res) {
                // $scope.errors = res.error;
            });

            $.unblockUI();
            $(args.modal).modal()
        });

        $rootScope.$on("resetFilter", function (event, args) {
            $scope.info = [];
        });

        $scope.drawChart = function(jsonData, chartId) {
            var data = new google.visualization.DataTable(
                    jsonData
            );

            var options = {
                'title': '',
                chartArea:{
                    left: 0,
                    top: 0,
                    width: "100%",
                    height: "100%"
                },
                'width': 700,
                'height': 400,
                legend: {
                    position: 'labeled'
                }
            };
            var chart = new google.visualization.PieChart(
                    document.getElementById(chartId)
            );
            chart.draw(data, options);
        };


        $scope.updateModalBodyHeight = function() {
            var modalBodyHeight = $(window).height() - 199;
            $('#myModalGeo .modal-body').css('max-height', modalBodyHeight + 'px');
        };


        $scope.updateModalBodyHeight();

        $(window).on('resize', function() {
            $scope.updateModalBodyHeight();
        });
    });

    app.controller('main', function($scope, $http, $rootScope){
        $scope.closeModalDialog = function(e){ closeModalDialog(e); };
        $scope.showModalDialog = function(target_selector){ showModalDialog(target_selector); };

        $scope.reverse = false;
        $scope.sortField = 'id';
        $scope.clients = [];
        $scope.currentClient = undefined;

        /* pagination */
        $scope.maxSize = 5;
        $scope.itemsPerPage = undefined;
        $scope.bigCurrentPage = 1;
        $scope.bigTotalItems = undefined;
        /* post */
        $scope.post = {};
        $scope.fields = [
            {'value': 'id', 'title': '№'},
            {'value': '', 'title': 'Client'},
            {'value': 'group', 'title': 'Group'},
            {'value': 'created_at', 'title': 'Created at'},
            {'value': 'last_activity', 'title': 'Last activity'},
            {'value': 'sys_ver', 'title': 'System'},
            {'value': 'ip', 'title': 'Ip'},
            {'value': 'country', 'title': 'Country'},
            {'value': 'client_ver', 'title': 'Version'},
            {'value': 'comments', 'title': 'Comments'}
        ];

        $scope.showPagination = function(){
            return $scope.bigTotalItems > $scope.itemsPerPage ? true : false;
        };

        $scope.basePost = function(){
            return {
                'sortField' : $scope.sortField,
                'reverse': $scope.reverse
            };
        };

        $scope.updPost = function(){
            $scope.post = $.extend($scope.post, $scope.basePost());
        };

        $scope.setDefaultValue = function(){
            $scope.bigTotalItems = undefined;
            $scope.bigCurrentPage = 1;
            $scope.clients = [];
        };

        $scope.getData = function(){
            $scope.updPost();
            $http.post('/rest/clients/filter/' + $scope.bigCurrentPage, $scope.post).then(function (res) {
              console.log('Post success on getData in clients/template/table.blade.php');
              if(res.data.hasOwnProperty('clients')) {
                  console.log('Response data: ');
                  console.log(res.data);
                  $scope.clients = res.data['clients'];
                  $scope.bigCurrentPage = res.data['current_page'];
                  $scope.bigTotalItems = res.data['total_items'];
                  $scope.itemsPerPage = res.data['items_per_page'];
              }else{
                  $scope.setDefaultValue();
              }
              $rootScope.$broadcast('total', $scope.bigTotalItems);
            }, function (res) {
              console.log('Post error on getData in clients/template/table.blade.php');
                // $scope.errors = res.error;
            });
        };

        $scope.sort = function(fieldName){
            if($scope.sortField === fieldName){
                $scope.reverse = !$scope.reverse;
            }else{
                $scope.sortField = fieldName;
                $scope.reverse = false;
            }
            $scope.getData();
        };

        $scope.isSortUp = function(fieldName){
            return $scope.sortField === fieldName && !$scope.reverse;
        };

        $scope.isSortDown = function(fieldName){
            return $scope.sortField === fieldName && $scope.reverse;
        };

        $rootScope.$on("applyFilter", function (event, args) {
            $scope.post = $scope.basePost();
            $scope.post = $.extend($scope.post, args);
            $scope.getData();
        });

        $rootScope.$on("resetFilter", function (event, args) {
            $scope.post = $scope.basePost();
            $scope.setDefaultValue();
            $rootScope.$broadcast('total', $scope.bigTotalItems);
        });

        $scope.showCommentsDialog = function(client){
            $scope.showModalDialog('.comments-modal');
            $scope.currentClient = client;
            $scope.currentCommentBeforeChange = client.comment.comment_text;
        };

        $scope.closeCommentsDialog = function(e){
            $scope.currentClient.comment.comment_text = $scope.currentCommentBeforeChange;
            $scope.closeModalDialog(e);
        };

        $scope.saveComment = function(comment_text){
            $http.post("/rest/clients/comment/", {
                clientid: $scope.currentClient.id,
                value: $scope.currentClient.comment.comment_text })
            .then(function (res) {
                $scope.closeModalDialog(1, "yes");
            })
        }; // end save
    });
</script>
