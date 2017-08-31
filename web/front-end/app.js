var vusalbaApp = angular.module('vusalbaApp',['ui.bootstrap','xeditable'])
    .config(['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    }]);
vusalbaApp.run(function (editableOptions) {
    editableOptions.theme = 'bs3';
});