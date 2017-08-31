vusalbaApp.controller('composantController',['$scope','$rootScope','composantService','$uibModal',
    function ($scope,$rootScope, composantService, $uibModal) {
        $scope.allComposants = {};
        $scope.allAxis = [];
        $scope.operateur = '';
        $scope.axe = '';
    $scope.getComposants = function (loading) {
        _show(loading);
        composantService.getAll()
            .then(function (response) {
                _hide(loading);
                console.log(response);
                // if (response.data) {
                //     $scope.allComposants = response.data;
                // }
            }, function (msg) {
                alert(msg);
            });
    };
    
    $scope.getAxes = function (loading) {
        _show(loading);
        composantService.getAllAxis()
            .then(function (response) {
                _hide(loading);
              //  console.log(response);
                  //  $scope.allAxis = response.data;
            }, function (msg) {
                alert(msg);
            })
    };
    $(document).on('change', 'input:checkbox[id^="iscalculated"]', function (event) {
        if ($('#iscalculated').is(':checked')) {
            _get('axis_list','','axeSelect');
            _get('op_list','','axeSelect2');
            $('#formula').show();
            $('#axeSelect').show();
            $('#axeSelect2').show();
        }else {
            $('#formula').hide();
            $('#axeSelect').hide();
            $('#axeSelect2').hide();
        }
    });
    $scope.createScope = function () {
        showNodeForm();
    }
    $scope.createEntity = function (loadind) {
        _showModal(loadind);
        composantService.createEntity()
            .then(function (response) {
                _hide(loadind);
                console.log(response);
            }, function (msg) {
                alert(msg);
            })
    }
}]);
