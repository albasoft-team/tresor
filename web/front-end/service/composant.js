'use strict';
vusalbaApp.factory('composantService', function ($http, $q) {
        var factory = {
                composants:false,
                axis:false,
                result:false,
                postreques : false,
                getAll : function () {
                    var  deferred = $q.defer();
                    if (factory.composants !== false) {
                        deferred.resolve(factory.composants);
                    }else {
                        $http.get(Routing.generate('all_comp'))
                            .then(function (data, status) {
                                factory.composants = data;
                                deferred.resolve(factory.composants);
                            },(function (data, status) {
                                deferred.reject('impossible de recuperer les donnees');
                            }));
                    }
                    return deferred.promise;
                },
                getAllAxis : function () {
                    var  deferred = $q.defer();
                    if (factory.axis !== false) {
                        deferred.resolve(factory.axis);
                    }else {
                        $http.get(Routing.generate('all_axis'))
                            .then(function (data, status) {
                                factory.axis = data;
                                deferred.resolve(factory.axis);
                            },(function (data, status) {
                                deferred.reject('impossible de recuperer les donnees');
                            }));
                    }
                    return deferred.promise;
                },
            createEntity : function () {
                var  deferred = $q.defer();
                if (factory.postreques !== false) {
                    deferred.resolve(factory.postreques);
                }else {
                    $http.post(Routing.generate('create_entity'), {})
                        .then(function (data, sataus) {
                            console.log(data);
                            factory.result = data;
                            deferred.resolve(factory.result);
                        }, function (data, status) {
                            deferred.reject('Erreur lors du traitement');
                        });
                }

                return deferred.promise;
            }
        };
        return factory;
});
