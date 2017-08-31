'use strict';
vusalbaApp.service('enterDataService', function ($http, $q) {

    var factory = {
        data : false,
        getAll : function () {
            var  deferred = $q.defer();
            if (factory.data !== false) {
                deferred.resolve(factory.data);
            }else {
                $http.get(Routing.generate('enter_data'))
                    .then(function (response) {
                        console.log(response);
                        factory.data = response;
                        deferred.resolve(factory.data);
                    }, function (response) {
                        deferred.reject('Impossible de recupérer les données');
                    })
            }
            return deferred.promise;
        },
        editData : function (donnees) {
            var  deferred = $q.defer();
            $http.post(Routing.generate('edit_data'), donnees)
                .then(function (data,status) {
                    factory.data = data;
                    deferred.resolve(factory.data);
                },(function (data) {
                    deferred.reject('impossible de recuperer les donnees')
                }));
            return deferred.promise ;
        },
        updateTable : function () {
            var  deferred = $q.defer();
            $http.post(Routing.generate('update_tags'), {})
                .then(function (data,status) {
                    factory.data = data;
                    deferred.resolve(factory.data);
                },(function (data) {
                    deferred.reject('impossible de recuperer les donnees')
                }));
            return deferred.promise ;
        }
    };
    return factory;
});
