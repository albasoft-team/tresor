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
        $rootScope.dataOfDrilldown = [];
        $rootScope.dataOfD = [];
        $scope.analyser = function (form) {
            if ($('#chartContainer').html() !== '' && $('#pieContainer').html() !== '') {
                $('#chartBlock').hide();
            }
            if ($('#tableauSynthese').html() !== '') {
              $('#tableauSynthese').css('display','none');
            }
            $rootScope.postecomptabelNbCheque = [];
            $rootScope.dataOfDrilldown = [];
            for (var i = 0 ; i < $scope.data.length ; i++) {
                    $scope.data[i]['hc-key'] == ''
                    $scope.data[i]['value'] = 0;
                    $scope.data[i]['drilldown'] = '';
                    $scope.data[i]['fils'] = [];

            }
            $scope.showTd = false;
            $scope.listTotalMt = [];
            $rootScope.nbChqTotal = 0 ;
            $rootScope.MntTotal = 0 ;
            nomcomp = form.composant.split('|')[1];
            nomaxe = form.axe.split('|')[1];
            nomaxe=(nomaxe=='Nombre total')?nomaxe :nomaxe+ ' (en millions)';
            divis=(nomaxe=='Nombre total')?0.000001 :1000000;
          $rootScope.nombreCheqTotal = 0;
          $rootScope.nombreMntTotal = 0;
          $rootScope.PcNbData = [];

            analyseService.postData(form)
                .then(function (response) {
                 // console.log(response);
                     $scope.results =response[1];
                     $rootScope.allData = $scope.results;
                    // console.log($scope.results);
                    if (response[0] == 0 || response[1].length == 0) {
                      $("#myModal").modal("show");
                        return;
                    }
                    $scope.listTotalMt = unique(response[0]);
                    //constructData($scope.listTotalMt);
                    //console.log($scope.listTotalMt);
                    $rootScope.PcNbData = $scope.listTotalMt;
                    // angular.forEach($rootScope.PcNbData, function (pc) {
                    //   angular.forEach(pc.fils, function (fils) {
                    //     fils
                    //   })
                    // })
                  angular.forEach($rootScope.PcNbData, function (it) {
                   // console.log(it);
                    $rootScope.nombreCheqTotal += it.value2;
                    $rootScope.nombreMntTotal += it.value*1000000;
                  });
                  $rootScope.nombreCheqTotal = lisibilite_nombre($rootScope.nombreCheqTotal);
                  $rootScope.nombreMntTotal = lisibilite_nombre($rootScope.nombreMntTotal);
                  setMask();
                  $('#tableauSynNat').css('display','block');
                    angular.forEach($rootScope.PcNbData, function (it) {
                       for (var i = 0 ; i < $scope.data.length ; i++) {
                           if ($scope.data[i]['hc-key'] == it.hc_key) {
                               $scope.data[i]['value'] = it.value;
                               $scope.data[i]['drilldown'] = it.name;
                               $scope.data[i]['fils'] = it.fils;
                           }
                       }
                    });
                  initialyze($scope.data, form);
                })

        };
        var initialyze = function (donnees, form) {
            $scope.nameofpoint = '';
            var titleAxis = '';
          $rootScope.dataOfDrilldown = [];
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
                            $rootScope.histoTitle = nomcomp;
                            $scope.isdrilldown = false;
                            $('#tableauSynNat').css('display','none');
                            $rootScope.testHello = "hello world";
                            //console.log(e.point);
                            $rootScope.dataOfDrilldown = [];
                            var form2 = angular.copy(form);
                            form2.axe = form2.axe.split('|')[0] == 'MontantTotal' ? 'NombreTotal|Nombre total' : 'MontantTotal|Montant total';
                            $rootScope.postecomptabelNbCheque = [];
                            $scope.formAxe = form.axe.split('|')[0] ;

                           // console.log($rootScope.dataOfDrilldown);
                          // dataTabSynthese($rootScope.PcNbData, e.point.options.drilldown);

                              analyseService.getTabSyntheseData(form2)
                                  .then(function (response) {
                                   // console.log(response);
                                    $rootScope.dataOfDrilldown = dataTabSynthese($rootScope.PcNbData, e.point.options.drilldown);
                                   // console.log($rootScope.dataOfDrilldown) ;
                                    $rootScope.nombreCheqTotal = 0;
                                    $rootScope.nombreMntTotal = 0;
                                    angular.forEach($rootScope.dataOfDrilldown, function (dt) {
                                      angular.forEach(dt.fils, function (d) {
                                        $rootScope.nombreCheqTotal += d.y2;
                                        $rootScope.nombreMntTotal += d.y*1000000;
                                      })
                                    })
                                    $rootScope.nombreCheqTotal = lisibilite_nombre($rootScope.nombreCheqTotal);
                                    $rootScope.nombreMntTotal = lisibilite_nombre($rootScope.nombreMntTotal);
                                    // getDataPlot(form);
                                     setMask();
                                   // $('#tableauSynthese').css('display','block');
                                     $rootScope.titre = e.point.name;


                                    // $scope.isdrilldown = e.point.options.fils[0].drilldown;
                                    //  console.log($scope.isdrilldown);

                                  });
                            //  console.log(e.point.fils);
                            var axisSelected = form.axe.split('|')[0] ;
                            $scope.DataPermute = angular.copy(e.point.fils);
                            //On permute les valeurs
                            if (axisSelected == 'NombreTotal') {
                              angular.forEach($scope.DataPermute, function (point) {
                                var copy = point.y;
                                point.y = parseInt(point.y2);
                                point.y2 = parseInt(copy);
                              })
                            }
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
                                  $('#tableauSynthese').css('display','block');
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
                                              $rootScope.histoTitle = e.point.name;
                                              $scope.nameofpoint = e.point.name;
                                                angular.forEach($rootScope.dataOfDrilldown[0].fils, function (it) {
                                                  angular.forEach(it.childs, function (child) {
                                                    $scope.isdrilldown = child.drilldown;
                                                  //  console.log(child);
                                                    drilldown.push({
                                                      name : child.name,
                                                      y : (form.axe.split('|')[1] == 'Montant total') ? child.y/divis : ((typeof child.y2 === "string") ? parseInt(child.y2) : child.y2),
                                                      drilldown : $scope.isdrilldown
                                                    })

                                                  })
                                                });
                                                var value = '{"'+ e.point.name +'":{'
                                                                        +'"name" :"'+ e.point.name + '",'
                                                                        +'"data" :'+ JSON.stringify(drilldown)
                                                                        +'}}';
                                                $scope.allfils = {};
                                                 $scope.allfils = JSON.parse(value);

                                                // console.log(e.point.fils);

                                                if (!e.seriesOptions) {
                                                    var chart = this,
                                                        drilldowns  = $scope.allfils;
                                                        series = drilldowns[e.point.name];
                                                       // console.log(series);
                                                        series.colorByPoint = true;
                                                        $scope.namechild = e.point.name;
                                                    // Show the loading label
                                                    chart.showLoading('<i class="icon-spinner icon-spin icon-3x"></i>');
                                                    setTimeout(function () {
                                                        chart.hideLoading();
                                                        chart.addSeriesAsDrilldown(e.point, series);
                                                    }, 2000);
                                                }
                                              //   var isdrill = $scope.isdrilldown ;
                                              // $scope.titleAxis = series.name;

                                            }
                                        }
                                    },
                                    title: {
                                        text: 'Histogramme des ' + $rootScope.histoTitle
                                    },
                                    xAxis: {
                                        type: 'category',
                                        title: {
                                            text :  ''
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
                                        data: axisSelected =='MontantTotal' ? e.point.fils : $scope.DataPermute
                                    }],
                                    drilldown : {
                                        series : [],
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
                                              angular.forEach($rootScope.dataOfDrilldown[0].fils, function (it) {
                                                angular.forEach(it.childs, function (child) {
                                                  $scope.isdrilldown = child.drilldown;
                                                  //console.log(child);
                                                  drilldown.push({
                                                    name : child.name,
                                                    y : (form.axe.split('|')[1] == 'Montant total') ? child.y/divis : ((typeof child.y2 === "string") ? parseInt(child.y2) : child.y2),
                                                    drilldown : $scope.isdrilldown
                                                  })

                                                })
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
                                        text: 'Camembert des '+ nomcomp
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
        };
        initialyze($scope.data, $scope.form);
        if (_getHtmlValue() !== 'container') {
            $('.highcharts-credits').text('');
        }
       function dataTabSynthese(PcNbData,pointname) {
          var dataObject = []
         angular.forEach(PcNbData, function (item) {
           if (item.name == pointname) {
             dataObject.push(item);
           }
         })
         $rootScope.titre = pointname;
          return dataObject;
       }
       $scope.closeExpande = function (it) {
         it.expanded = false;
         $scope.showTd = false
         angular.forEach(it.fils, function (item) {
           item.expanded = false;
         })
       }
       $scope.openExpande = function (it) {
          $scope.showTd = true;
         it.expanded = true;
       }
        function getDataPlot(form) {
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
                        valeur : typeof it.valeurAxe === "string" ? parseInt(it.valeurAxe) : it.valeurAxe
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
                  //console.log(filsMt);
                  obj = '{'
                    +'"id" :"'+ data_id + '",'
                    +'"name" :"'+ filsMt.name + '",'
                    +'"description" :"'+ filsMt.description + '",'
                    +'"nbChq" :'+ filsNb.y + ','
                    +'"Mtotal" :'+ filsMt.y*divis + ','
                    +'"fils" :'+ JSON.stringify(childrens)
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
                          childrens[index].valeur = typeof it2.valeurAxe === "string" ? parseInt(it2.valeurAxe) : it2.valeurAxe;
                        }
                      })
                    }
                  });
                 // console.log(filsNb);

                  obj = '{'
                    +'"id" :"'+ data_id + '",'
                    +'"name" :"'+ filsMt.name + '",'
                    +'"description" :"'+ filsNb.description + '",'
                    +'"nbChq" :'+ filsMt.y + ','
                    +'"Mtotal" :'+ filsNb.y*1000000 + ','
                    +'"fils" :'+ JSON.stringify(childrens)
                    +'}';
                }
              }
            });
            $rootScope.postecomptabelNbCheque.push(JSON.parse(obj));
          });

         // console.log($rootScope.postecomptabelNbCheque);
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