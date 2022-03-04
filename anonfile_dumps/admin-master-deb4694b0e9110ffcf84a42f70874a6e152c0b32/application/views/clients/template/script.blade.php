<script type="text/javascript">
    $('#group, #events, #events_module, #country').select2({
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);

    $('#log, #events_info, #sysinfo').select2({
        tags: true,
        allowClear: true,
        tokenSeparators: [',']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);

    $(function () {
        $('#log_start, #log_end').datetimepicker({
            locale: 'ru',
            format: 'YYYY/MM/D HH:mm'
        });
        $("#log_start, #log_end").on("dp.change", function (e) {
            $('#log_start').trigger("change");
        });

    });

    app.controller('clientFilter', function($scope, $rootScope, $http, $location){
        $scope.closeModalDialog = function(e){ closeModalDialog(e); };
        $scope.showModalDialog = function(target_selector){ showModalDialog(target_selector); };

        $scope.filter = {};
        $scope.filter.additionalEnabled = false;
        $scope.filter.eventsEnabled = false;
        $scope.filter.exactMatchingSearch = true;
        $scope.pushBackBtn = true;
        $scope.total = 0;

        $scope.pushBack = function(){
            $scope.post = {
                'post': $scope.filter,
                'params': $scope.modalParams,
                'incode': $scope.modalIncode
            };
            $http.post('/rest/clients/push_back', $scope.post);
        };

        $scope.apply = function(){
            $rootScope.$broadcast("applyFilter", $scope.filter);
        };

        $scope.reset = function(){
            $('.selectpicker').val(null).selectpicker('refresh');
            $("#group, #events, #country #sysinfo").val(null).trigger('change.select2');
            // todo work auto in develop, but not in production
            $('.select2-selection__clear').remove();
            $('#log_start, #log_end').data('DateTimePicker').clear();
            $scope.total = 0;
            $scope.pushBackBtn = true;

            $scope.filter = {};
            $rootScope.$broadcast("resetFilter");
            angular.forEach($scope.filter, function(value,index){
                $scope.filter[index] = null;
            });
        };

        $rootScope.$on("getPostFilter", function (event, args) {
            $rootScope.$broadcast('forStat', {
                'post': $scope.filter,
                'chart': args.chart,
                'modal': args.modal,
                'type': args.type,
                'url': args.url
            });
        });

        $rootScope.$on("total", function (event, args) {
            $scope.total = args;
            /* pushBack btn disbl/enbl */
            $scope.pushBackBtn = !args;
        });

        var getParams = new URL(window.location.href).searchParams.get("params_as");
        if(getParams != null){
            $scope.filter.log = [getParams];
            $('#log').select2({data: [getParams]})
                .val(getParams)
                .trigger('change.select2')
                .add($scope.apply);
            console.log($scope.filter.log);
        }

        $scope.changeSearchType = function(e){
            var current_target = e.target || e.currentTarget;
            if ($(current_target).parents(".dialog").length > 0){
                $scope.filter.exactMatchingSearch = false;
                $scope.closeModalDialog(e);
            }
            else {
                if ($scope.filter.exactMatchingSearch){
                    $scope.filter.exactMatchingSearch = true; }
                else {
                    $scope.filter.exactMatchingSearch = true;
                    $scope.showModalDialog(".alert-modal"); }
            }
        };
    });

    app.controller('clientIdFilter', function($scope, $http, $window){
        $scope.clientId = undefined;
        $scope.errors = undefined;

        $scope.apply = function(){
            $scope.errors = undefined;
            $http.post('/rest/clients/client_filter', {'client_id': $scope.clientId}).success(function(data){
                if(data.errors != undefined){
                    $scope.errors = data.errors;
                }else if(data.url != undefined){
                    $window.open(data.url, '_blank');
                }
            });
        };
    });
</script>


<script type="text/javascript">
    app.controller('clientTable', function($scope, $http, $rootScope){
        $scope.reverse = false;
        $scope.sortField = null;
        $scope.clients = [];
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
            {'value': '', 'title': 'Nat'},
            {'value': 'group', 'title': 'Group'},
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'last_activity', 'title': 'LastActivity'},
            {'value': 'importance', 'title': 'Importance'},
            {'value': 'sys_ver', 'title': 'System'},
            {'value': 'ip', 'title': 'Ip'},
            {'value': 'country', 'title': 'Country'},
            {'value': 'client_ver', 'title': 'Version'}
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
            swal({
                title: "Loading...",
                showConfirmButton: false
            });
            $http.post('/rest/clients/filter/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                swal.close();

                if(data.hasOwnProperty('clients')) {
                    $scope.clients = data['clients'];
                    $scope.bigCurrentPage = data['current_page'];
                    $scope.bigTotalItems = data['total_items'];
                    $scope.itemsPerPage = data['items_per_page'];
                }else{
                    $scope.setDefaultValue();
                }
                $rootScope.$broadcast('total', $scope.bigTotalItems);
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
    });
</script>

<script type="text/javascript">
	$('.datepicker-range').datepicker({
		language: "ru",
		autoclose: true,
		todayHighlight: true,
		format: "yyyy/mm/dd"
	}).on('changeDate', function(dateEvent) {
		var start = $('#datepicker').datepicker('startDate');
	});
	$('.selectpicker').selectpicker({
        style: 'btn-default btn-sm'
    });

	$('#myModal').on('show.bs.modal', function () {
		$('.modal-body').css('max-height',$( window ).height()*0.8);
	});
</script>



<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart']});

    app.controller('statBtn', function($scope, $rootScope){
        $scope.ipStatBtn = function(){
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/ip/',
                'chart': 'chart',
                'type': 'Ip',
                'modal': '#myModalStat'
            });
        };

        $scope.geoStatBtn = function(){
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/geo/',
                'chart': 'chart',
                'type': 'Geo',
                'modal': '#myModalStat'
            });
        };

        $scope.systemStatBtn = function(){
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/system/',
                'chart': 'chart',
                'type': 'System',
                'modal': '#myModalStat'
            });
        };

        $scope.userSystemStatBtn = function(){
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/usersystem/',
                'chart': 'chart',
                'type': 'UserSystem',
                'modal': '#myModalStat'
            });
        };

        $scope.avStatBtn = function(){
            $rootScope.$broadcast("getPostFilter", {
                'url': '/rest/stats/av/',
                'chart': 'chart',
                'type': 'Av',
                'modal': '#myModalStat'
            });
        };
    });

    app.controller('stat', function($scope, $rootScope, $http){
        $scope.info = [];
        $scope.type = undefined;

        $rootScope.$on("forStat", function (event, args) {
            swal({
                title: "Loading...",
                showConfirmButton: false
            });
            $http.post(args.url, args.post).success(function(data){
                if(data.detailedStat != undefined)
                    $scope.info = data.detailedStat;

                $scope.drawChart(data, args.chart);
                $scope.type = args.type;

                swal.close();
                $(args.modal).modal();
            });
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
                backgroundColor: '#444',
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
            $('#myModalStat .modal-body').css('max-height', modalBodyHeight + 'px');
        };


        $scope.updateModalBodyHeight();

        $(window).on('resize', function() {
            $scope.updateModalBodyHeight();
        });
    });
</script>
