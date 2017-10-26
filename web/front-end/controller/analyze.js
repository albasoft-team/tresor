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
       var nomcomp ='', nomaxe = '', divis=1;
        $rootScope.postecomptabelNbCheque = [];
        $scope.analyser = function (form) {
            if ($('#chartContainer').html() !== '' && $('#pieContainer').html() !== '') {
                $('#chartBlock').hide();
            }
            $rootScope.postecomptabelNbCheque = [];
            for (var i = 0 ; i < $scope.data.length ; i++) {
                    $scope.data[i]['hc-key'] == ''
                    $scope.data[i]['value'] = 0;
                    $scope.data[i]['drilldown'] = '';
                    $scope.data[i]['fils'] = [];

            }
            $scope.listTotalMt = [];
            nomcomp = form.composant.split('|')[1];
            nomaxe = form.axe.split('|')[1];
            nomaxe=(nomaxe=='Nb cheques')?nomaxe :nomaxe+ ' (en millions)';
            divis=(nomaxe=='Nb cheques')?0.000001 :1000000;
            // console.log('divis='+divis);
            analyseService.postData(form)
                .then(function (response) {
                     $scope.results =response[1];
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
                                form2.axe = form2.axe.split('|')[0] == 'MontantTotal' ? 'NbCheques|Nb cheques' : 'MontantTotal|Montant total';
                                $rootScope.postecomptabelNbCheque = [];
                                analyseService.postData(form2)
                                    .then(function (response) {
                                        $scope.listTotalNbChq = unique(response[0]);
                                        // console.log(e.point.hc_key);
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
                                        angular.forEach($scope.TotalMT[0].fils, function (filsMt) {
                                            var obj = "";
                                            angular.forEach($scope.TotalNbC[0].fils, function (filsNb) {
                                                if (filsNb.name == filsMt.name) {
                                                    if (form.axe.split('|')[0] == "MontantTotal") {
                                                        obj = '{'
                                                            +'"name" :"'+ filsMt.name + '",'
                                                            +'"nbChq" :'+ filsNb.y + ','
                                                            +'"Mtotal" :'+ filsMt.y
                                                            +'}';
                                                    }
                                                    if (form.axe.split('|')[0] == "NbCheques") {
                                                        obj = '{'
                                                            +'"name" :"'+ filsMt.name + '",'
                                                            +'"nbChq" :'+ filsMt.y + ','
                                                            +'"Mtotal" :'+ filsNb.y
                                                            +'}';
                                                    }

                                                }
                                            });
                                            $rootScope.postecomptabelNbCheque.push(JSON.parse(obj));
                                        });
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
                                                            // console.log(it.valeurAxe / divis);
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
                                            series : []
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
                                                                y : it.valeurAxe,
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
                                this.setTitle(null, { text: e.point.name });
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


            };
            $scope.initialyze($scope.data, $scope.form);
        if (_getHtmlValue() !== 'container') {
            $('.highcharts-credits').text('');
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