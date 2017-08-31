'use strict';
vusalbaApp.factory('analyseService', function ($http, $q) {
    var factory = {
        results : false,
        postData : function (formData) {
            var deferred = $q.defer();
            $http.post(Routing.generate('postdata_analyse'), formData)
                .then(function (response) {
                    factory.results = response.data;
                    deferred.resolve(factory.results);
                }, function (data) {
                    deferred.reject('impossible de recupérer les données !!!')
                })
            return deferred.promise;
        },
        getResultData : function () {
            var deferred = $q.defer();
            $http.post(Routing.generate('analyse_data'))
                .then(function (response) {
                    factory.results = response.data;
                    deferred.resolve(factory.results);
                }, function (data) {
                    deferred.reject('impossible de recupérer les données !!!')
                })
            return deferred.promise;
        }
    };
    return factory;
});
