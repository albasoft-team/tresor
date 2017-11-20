vusalbaApp.controller('analyzeController',['$scope','$rootScope', 'analyseService',
    function ($scope, $rootScope, analyseService) {
    $scope.data = [
                    ['sn-sl', 0],
                    ['sn-th', 0],
                    ['sn-680', 0],
                    ['sn-zg', 0],
                    ['sn-tc', 0],
                    ['sn-kd', 0],
                    ['sn-6976', 0],
                    ['sn-6978', 0],
                    ['sn-6975', 0],
                    ['sn-dk', 0],
                    ['sn-db', 0],
                    ['sn-fk', 0],
                    ['sn-1181', 0],
                    ['sn-lg', 0]
                    ];
       $scope.form = {};
       $scope.parent = 0;
       var nomcomp ='', nomaxe = '', divis=1;
        $rootScope.postecomptabelNbCheque = [];
        $scope.analyser = function (form) {
            if ($('#chartContainer').html() !== '' && $('#pieContainer').html() !== '') {
                $('#chartBlock').hide();
            }
            if ($('#tableauSynthese').html() !== '') {
              $('#tableauSynthese').css('display','none');
            }
            $rootScope.postecomptabelNbCheque = [];
            for (var i = 0 ; i < $scope.data.length ; i++) {
                    $scope.data[i]['hc-key'] == ''
                    $scope.data[i]['value'] = 0;
                    $scope.data[i]['drilldown'] = '';
                    $scope.data[i]['fils'] = [];

            }
            $scope.listTotalMt = [];
            $rootScope.nbChqTotal = 0 ;
            $rootScope.MntTotal = 0 ;
            nomcomp = form.composant.split('|')[1];
            nomaxe = form.axe.split('|')[1];
            nomaxe=(nomaxe=='Nombre total')?nomaxe :nomaxe+ ' (en millions)';
            divis=(nomaxe=='Nombre total')?0.000001 :1000000;


            analyseService.postData(form)
                .then(function (response) {
                     $scope.results =response[1];
                    if (response[0] == 0 || response[1].length == 0) {
                      $("#myModal").modal("show");
                        return;
                    }
                    $scope.listTotalMt = unique(response[0]);
                    angular.forEach(response[0], function (it) {
                       for (var i = 0 ; i < $scope.data.length ; i++) {
                           if ($scope.data[i]['hc-key'] == it.hc_key) {
                               $scope.data[i]['value'] = it.value;
                               $scope.data[i]['drilldown'] = it.name;
                               $scope.data[i]['fils'] = it.fils;
                           }
                       }
                    });
                    $scope.initialyze($scope.data, form);
                })

        };
            $scope.initialyze = function (donnees, form) {
                $scope.nameofpoint = '';

                Highcharts.mapChart('container', {
                    chart: {
                        map: 'countries/sn/sn-all',
                        events: {
                            load: function () {
                                this.mapZoom(1, 100, 100);
                            },
                            drilldown: function (e) {
                                $scope.nameofpoint = e.point.name;
                                $scope.listTotalNbChq = [];
                                $scope.TotalNbC = [];
                                $scope.TotalMT = [];
                                var form2 = angular.copy(form);
                                form2.axe = form2.axe.split('|')[0] == 'MontantTotal' ? 'NombreTotal|Nombre total' : 'MontantTotal|Montant total';
                                $rootScope.postecomptabelNbCheque = [];
                              $scope.formAxe = form.axe.split('|')[0] ;
                                analyseService.postData(form2)
                                    .then(function (response) {
                                        $scope.listTotalNbChq = unique(response[0]);
                                        $scope.resultForm = response[1];
                                        angular.forEach($scope.listTotalNbChq, function (nb) {
                                             if (nb.name == e.point.options.drilldown) {
                                                $scope.TotalNbC.push(nb);
                                            }
                                        });
                                        angular.forEach($scope.listTotalMt, function (mt) {
                                            if (mt.name == e.point.options.drilldown) {
                                                $scope.TotalMT.push(mt);
                                            }
                                        });
                                        if (form.axe.split('|')[0] == "MontantTotal") {
                                            $rootScope.nbChqTotal = lisibilite_nombre($scope.TotalNbC[0].value);
                                            $rootScope.MntTotal = lisibilite_nombre($scope.TotalMT[0].value*1000000);
                                        }
                                        if (form.axe.split('|')[0] == "NombreTotal") {
                                            $rootScope.nbChqTotal = lisibilite_nombre($scope.TotalMT[0].value) ;
                                            $rootScope.MntTotal = lisibilite_nombre($scope.TotalNbC[0].value*1000000) ;
                                        }

                                        angular.forEach($scope.TotalMT[0].fils, function (filsMt) {
                                            var obj = "";
                                            angular.forEach($scope.TotalNbC[0].fils, function (filsNb) {
                                                if (filsNb.name == filsMt.name) {
                                                  var childrens = [];
                                                  var data_id = 0;

                                                    if (form.axe.split('|')[0] == "MontantTotal") {
                                                      angular.forEach($scope.results, function (it) {
                                                        if (it.parent !== null && it.parent == filsNb.name) {
                                                          data_id = it.parentId;
                                                          childrens.push({
                                                            id : it.parentId,
                                                            name : it.name,
                                                            valeur : (nomaxe == 'Montant total') ? it.valeurAxe/divis : ((typeof it.valeurAxe === "string") ? parseInt(it.valeurAxe) : it.valeurAxe)
                                                          })
                                                        }
                                                      });
                                                      angular.forEach($scope.resultForm, function (it2) {
                                                        if (it2.parent !== null && it2.parent == filsNb.name) {
                                                          angular.forEach(childrens, function (child, index) {
                                                            if (child.name == it2.name) {
                                                              childrens[index].nbTotal = it2.valeurAxe;
                                                            }
                                                          })
                                                        }
                                                      });
                                                        obj = '{'
                                                            +'"id" :"'+ data_id + '",'
                                                            +'"name" :"'+ filsMt.name + '",'
                                                            +'"nbChq" :'+ filsNb.y + ','
                                                            +'"Mtotal" :'+ filsMt.y*divis + ','
                                                            +'"childrens" :'+ JSON.stringify(childrens)
                                                            +'}';
                                                    }
                                                    if (form.axe.split('|')[0] == "NombreTotal") {
                                                      angular.forEach($scope.results, function (it) {
                                                        if (it.parent !== null && it.parent == filsNb.name) {
                                                          data_id = it.parentId;
                                                          childrens.push({
                                                            id : it.parentId,
                                                            name : it.name,
                                                            nbTotal : it.valeurAxe
                                                          })
                                                        }
                                                      });
                                                      angular.forEach($scope.resultForm, function (it2) {
                                                        if (it2.parent !== null && it2.parent == filsNb.name) {
                                                          angular.forEach(childrens, function (child, index) {
                                                            if (child.name == it2.name) {
                                                              childrens[index].valeur =(nomaxe == 'Montant total') ? it2.valeurAxe/divis : ((typeof it2.valeurAxe === "string") ? parseInt(it2.valeurAxe) : it2.valeurAxe);
                                                            }
                                                          })
                                                        }
                                                      });

                                                        obj = '{'
                                                            +'"id" :"'+ data_id + '",'
                                                            +'"name" :"'+ filsMt.name + '",'
                                                            +'"nbChq" :'+ filsMt.y + ','
                                                            +'"Mtotal" :'+ filsNb.y*1000000 + ','
                                                            +'"childrens" :'+ JSON.stringify(childrens)
                                                            +'}';
                                                    }
                                                }
                                            });
                                            $rootScope.postecomptabelNbCheque.push(JSON.parse(obj));
                                        });
                                       setMask();
                                      $('#tableauSynthese').css('display','block');
                                       $rootScope.titre = e.point.name;

                                    });

                                if (!e.seriesOptions) {
                                    var chart = this;
                                    // Show the spinner
                                    chart.showLoading('<i class="icon-spinner icon-spin icon-3x"></i>');
                                    // chart.showLoading("<img src='web/img/loader.gif' alt='chargement' />");
                                    setTimeout(function () {
                                        chart.hideLoading();
                                        if ($('#chartBlock').is(':hidden')) {
                                            _show('chartBlock');
                                        }
                                    }, 2000);
                                  Highcharts.setOptions({
                                    lang: {
                                      drillUpText: 'Retour'
                                    }
                                  });
                                    Highcharts.chart('chartContainer', {
                                        chart: {
                                            type: 'column',
                                            events : {
                                                drilldown : function (e) {
                                                    e.point.fils = {}; var drilldown = [];
                                                    $scope.nameofpoint = e.point.name;
                                                    angular.forEach($scope.results, function (it) {
                                                        if (it.parent !== null && it.parent == e.point.name) {
                                                            var isdrilldown = it.level == form.level ? false : true;
                                                            drilldown.push({
                                                                name : it.name,
                                                                y : (nomaxe == 'Montant total') ? it.valeurAxe/divis : ((typeof it.valeurAxe === "string") ? parseInt(it.valeurAxe) : it.valeurAxe),
                                                                drilldown : isdrilldown
                                                            })
                                                        }

                                                    });
                                                    var value = '{"'+ e.point.name +'":{'
                                                                            +'"name" :"'+ e.point.name + '",'
                                                                            +'"data" :'+ JSON.stringify(drilldown)
                                                                            +'}}';
                                                    $scope.allfils = {};
                                                     $scope.allfils = JSON.parse(value);
                                                    if (!e.seriesOptions) {
                                                        var chart = this,
                                                            drilldowns  = $scope.allfils;
                                                            series = drilldowns[e.point.name];
                                                            series.colorByPoint = true;
                                                        // Show the loading label
                                                        chart.showLoading('<i class="icon-spinner icon-spin icon-3x"></i>');
                                                        setTimeout(function () {
                                                            chart.hideLoading();
                                                            chart.addSeriesAsDrilldown(e.point, series);
                                                        }, 2000);
                                                    }

                                                }
                                            }
                                        },
                                        title: {
                                            text: 'Répertoire des '+ nomcomp
                                        },
                                        xAxis: {
                                            type: 'category',
                                            title: {
                                                text : 'Postes comptables de la région de '+e.point.name
                                            }
                                        },
                                        yAxis: {
                                            title: {
                                                text: nomaxe
                                            }

                                        },
                                        legend: {
                                            enabled: false
                                        },
                                        plotOptions: {
                                            series: {
                                                pointWidth : 30,
                                                borderWidth: 0,
                                                dataLabels: {
                                                    enabled: true,
                                                    // format: '{point.y:.1}'
                                                }
                                            }
                                        },
                                        exporting: { enabled: false },
                                        tooltip: {
                                            // headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                                            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
                                        },
                                        series: [{
                                            name: '',
                                            colorByPoint: true,
                                            data: e.point.fils
                                        }],
                                        drilldown : {
                                            // series : $scope.allfils
                                            series : [],
                                          drillUpButton : {
                                            align: "right",
                                            verticalAlign:"top",
                                            x:0,
                                            y:0
                                          }
                                        }
                                    })
                                    Highcharts.chart('pieContainer', {
                                        chart: {
                                            plotBackgroundColor: null,
                                            plotBorderWidth: null,
                                            plotShadow: false,
                                            type: 'pie',
                                            events : {
                                                drilldown : function (e) {
                                                    e.point.fils = {}; var drilldown = [];
                                                    $scope.nameofpoint = e.point.name;
                                                    angular.forEach($scope.results, function (it) {
                                                        if (it.parent !== null && it.parent == e.point.name) {
                                                            var isdrilldown = it.level == form.level ? false : true
                                                            drilldown.push({
                                                                name : it.name,
                                                                y : it.valeurAxe/divis,
                                                                drilldown : isdrilldown
                                                            })
                                                        }

                                                    });
                                                    var value = '{"'+ e.point.name +'":{'
                                                        +'"name" :"'+ e.point.name + '",'
                                                        +'"data" :'+ JSON.stringify(drilldown)
                                                        +'}}';
                                                    $scope.allfils = {};
                                                    $scope.allfils = JSON.parse(value);
                                                    if (!e.seriesOptions) {
                                                        var chart = this,
                                                            drilldowns  = $scope.allfils;
                                                        series = drilldowns[e.point.name];
                                                        // Show the loading label
                                                        chart.showLoading('<i class="icon-spinner icon-spin icon-3x"></i>');
                                                        setTimeout(function () {
                                                            chart.hideLoading();
                                                            chart.addSeriesAsDrilldown(e.point, series);
                                                        }, 2000);
                                                    }
                                                }
                                            }
                                        },
                                        title: {
                                            text: 'Postes comptables de la région de '+ $scope.nameofpoint
                                        },
                                        exporting: { enabled: false },
                                        plotOptions: {
                                            pie: {
                                                allowPointSelect: true,
                                                cursor: 'pointer',
                                                dataLabels: {
                                                    enabled: false
                                                },
                                                showInLegend: true
                                            }
                                        },
                                        tooltip: {
                                            pointFormat: '<b>{point.percentage:.1f}%</b>'
                                        },
                                        series: [{
                                            name: nomaxe,
                                            colorByPoint: true,
                                            data: e.point.fils
                                        }],
                                        drilldown : {
                                            // series : $scope.allfils
                                            series : []
                                        }
                                    });
                                }
                                // this.setTitle(null, { text: e.point.name });
                                if (_getHtmlValue('pieContainer') !== '') {
                                    $('.highcharts-credits').text('');
                                }
                                if (_getHtmlValue('chartContainer') !== '') {
                                    $('.highcharts-credits').text('');
                                }

                            }
                        }
                    },
                    title: {
                        text: 'Représentation niveau national'
                    },

                    subtitle: {
                        text: "Senegal"
                    },

                    mapNavigation: {
                        enabled: true,
                        buttonOptions: {
                            verticalAlign: 'bottom'
                        }
                    },

                    colorAxis: {
                        min: 0
                    },

                    series: [{
                        data: donnees,
                        name: nomaxe,
                        states: {
                            hover: {
                                color: '#BADA55'
                            }
                        },
                        // exporting: { enabled: false },
                        dataLabels: {
                            enabled: true,
                            format:  '{point.name}'
                        }
                    }]
                });


                // angular.element(document.getElementsByClassName('nombreMask')).mask('000 000 000 000 000', {reverse: true})
                // angular.element(document.getElementsByClassName('nombreMask')).mask('#00 000 000 000 000', {reverse: true})
                // angular.element(document.getElementsByClassName('nombreMask')).mask('##0 000 000 000 000', {reverse: true})
            };
            $scope.initialyze($scope.data, $scope.form);
        if (_getHtmlValue() !== 'container') {
            $('.highcharts-credits').text('');
        }
        function setMask() {
            $('.nombreMask').mask('000 000 000 000 000', {reverse: true});
            // $('.nombreMask').mask('#00 000 000 000 000', {reverse: true});
            // $('.nombreMask').mask('##0 000 000 000 000', {reverse: true});
        }
        function lisibilite_nombre(nbr)
        {
            var nombre = ''+nbr;
            var retour = '';
            var count=0;
            for(var i=nombre.length-1 ; i>=0 ; i--)
            {
                if(count!=0 && count % 3 == 0)
                    retour = nombre[i]+' '+retour ;
                else
                    retour = nombre[i]+retour ;
                count++;
            }
            return retour;
        }
        var unique = function (arr) {
            var arrResult = {};
            for (i = 0, n = arr.length; i < n; i++) {
                var item = arr[i];
                arrResult[ item.hc_key + " - " + item.name ] = item;
            }
            var i = 0;
            var nonDuplicatedArray = [];
            for(var item in arrResult) {
                nonDuplicatedArray[i++] = arrResult[item];
            }
            return nonDuplicatedArray;
        }
    }]);