<script type="text/javascript">
    app.controller('table', function($scope){
        $scope.reverse = false;
        $scope.resetForm = false;
        $scope.sortField = null;
        $scope.maxSize = 5;
        $scope.itemsPerPage = undefined;
        $scope.bigCurrentPage = 1;
        $scope.bigTotalItems = undefined;
        $scope.post = {};
        $scope.filter = {
            online: "1"
        };
        $scope.data = {};

        $scope.sendPost = function(){};
        $scope.ifReset = function(){};

        $scope.basePost = function(){
            return {
                'sortField' : $scope.sortField,
                'reverse': $scope.reverse
            };
        };

        $scope.updPost = function(){
            $scope.post = $.extend($scope.post, $scope.basePost());
        };

        $scope.getData = function(){
            if(!$scope.resetForm){
                $scope.updPost();
                $scope.sendPost();
            }else{
                $scope.resetForm = false;
            }
        };

        $scope.showPagination = function(){
            return $scope.bigTotalItems > $scope.itemsPerPage ? true : false;
        };

        /* btn */
        $scope.apply = function(){
            $scope.post = $scope.basePost();
            $scope.post = $.extend($scope.post, $scope.filter);
            $scope.getData();
        };

        $scope.resetBefore = function(){};
        $scope.reset = function(){
            $scope.resetBefore();
            $scope.ifReset();
            $scope.resetForm = true;
            $scope.post = $scope.basePost();
            $scope.data = {};
            $scope.bigTotalItems = undefined;

            angular.forEach($scope.filter, function(value,index){
                $scope.filter[index] = null;
            });
        };

        /* sort */
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
    });
</script>