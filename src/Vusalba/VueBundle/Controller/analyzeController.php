<?php
/**
 * Created by PhpStorm.
 * User: Aly Seck
 * Date: 18/07/2017
 * Time: 14:54
 */

namespace Vusalba\VueBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vusalba\VueBundle\Constante\ChartConstante;
use Vusalba\VueBundle\Constante\Constante;
use Vusalba\VueBundle\Entity\Node;

/**
 * Class analyzeController
 * @package Vusalba\VueBundle\Controller
 * @Route("analyser")
 *
 */
class analyzeController extends Controller
{

    /**
     * @Route("/", name="analyze_index", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $axes = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        $levels = $em->getRepository('VueBundle:Level')->findBy(array(
            'scopeAnalysis' => true
        ));
        $groupes = $em->getRepository('VueBundle:Groupe')->findAll();

        return $this->render('analyze/index.html.twig', array(
            "axes" => $axes,
            'levels' => $levels,
            'groupes' => $groupes
        ));
    }

   /**
    * @param Request $request
    * @Route("/temp", name="postdata_temp", options={"expose"=true})
    * @Method({"GET", "POST"})
    * @return JsonResponse
    */
    public function getDataTemp(Request $request) {
       $response = [];
       $serializer = $this->get('serializer');
       $arraResults = $serializer->normalize($response);
       return new JsonResponse([$arraResults]);
    }
   /**
    * @Route("/analyser", name="postdata_analyse", options={"expose" = true})
    * @Method("POST")
    * @param Request $request
    * @return JsonResponse
    */
    public function postDataAnalyse(Request $request) {
        ini_set('memory_limit','2048M');
        set_time_limit(600);
        $data = json_decode($request->getContent(), TRUE);
        $composant =  explode('|', $data['composant'])[0];
        $axe = explode('|',$data['axe'])[0];
        $niveau = explode('|',$data['level'])[0];
        $levelname = explode('|',$data['level'])[1];
        $em = $this->getDoctrine()->getManager();
        $comp = $em->getRepository('VueBundle:Composant')->find($composant);
        if ( (array_key_exists('startDate', $data) &&  $data['startDate'] !== '') &&
             (array_key_exists('endDate', $data) && $data['endDate'])  ) {
            $dateDebut = $data['startDate'];
            $dateFin = $data['endDate'];
            $startDate = explode('/', $dateDebut);
            $endDate = explode('/', $dateFin);
            $str_startDate = $startDate[2] . $startDate[1] . $startDate[0] ;
            $str_endDate = $endDate[2] . $endDate[1] . $endDate[0] ;
            $results = $this->getDoctrine()->getRepository('VueBundle:InputTable')
                ->getResultByStartAndEndDate($comp,$str_startDate, $str_endDate);
        }
        else {
            $results = $em->getRepository('VueBundle:InputTable')
                ->findBy(array('composant' => $comp));
        }

        $arrayResults = [];

        foreach ($results as $result) {
            if ($result->getNode()->getLevel() == $niveau) {
                array_push($arrayResults, array(
                    'tags' => $result->getTags(),
                    'composant' => $comp->getId(),
                    'node' => $result->getNode()->getId()
                ));
            }
        }
        $arrayNodes = [];
        $listNoeud = [];
//       $conn =  $this->getDoctrine()->getConnection();
//       $arrayResults = Constante::queryTest($conn);
       // $listNoeuds = [];
       foreach ($arrayResults as $result) {
           $node = $em->getRepository('VueBundle:Node')->find($result['node']);
           $jsonobject = json_decode($result['tags']);
           $valeurAxe = 0;
           $secondValeurAxe = 0;
           //var_dump($result);
            foreach ($jsonobject->axeValues as $axeValue ) {
              // var_dump($axeValue);
                if ($axeValue->code == 'MontantTotal') {
                    $valeurAxe = str_replace(" ","", $axeValue->value);
                }
                if ($axeValue->code == 'NombreTotal') {
                   $secondValeurAxe = str_replace(" ","", $axeValue->value);
                }
            }
            $this->constructList($node, $listNoeud, $valeurAxe, $secondValeurAxe);
//            $this->getResultData($listNoeud);
          //bricolage
          $arrayTemp = array();
          foreach ($listNoeud as $key => $lst) {
             $exist = false;
             foreach ($arrayTemp as $key2 => $item) {
                if ($item['node'] == $lst['node']) {
                   $exist = true;
                   $arrayTemp[$key2]['valeurAxe'] += $listNoeud[$key]['valeurAxe'];
                   $arrayTemp[$key2]['valeurAxe2'] += $listNoeud[$key]['valeurAxe2'];
                }
             }
             if ($exist == false) {
                array_push($arrayTemp, $lst);
             }
          }
          $listNoeud = $arrayTemp;
//          foreach ($listNoeud as $key => $lst) {
//               foreach ($listNoeud as $key2 => $lst2) {
//                   if ($key!=$key2) {
//                       if ($listNoeud[$key]['node'] == $listNoeud[$key2]['node']) {
//                          $listNoeud[$key]['valeurAxe'] += $listNoeud[$key2]['valeurAxe'];
//                          $listNoeud[$key]['valeurAxe2'] += $listNoeud[$key2]['valeurAxe2'];
//                          $listNoeud[$key2]['valeurAxe'] = 0;
//                          $listNoeud[$key2]['valeurAxe2'] = 0;
//                       }
//                   }
//               }
//          }
       }
        $response = $this->getDataSource($listNoeud, $niveau, $composant,$axe, $levelname);
        $serializer = $this->get('serializer');
        $arraResults = $serializer->normalize($response);
        return new JsonResponse([$arraResults, $listNoeud]);
    }

    /**
     * @param $arrayResults
     * @return JsonResponse
     * @Route("/getData", name="analyse_data", options={"expose" = true})
     * @Method("GET")
     */
    public function getResultData($arrayResults) {
        $serializer = $this->get('serializer');
        $arraResults = $serializer->normalize($arrayResults);

        return new JsonResponse($arraResults);
    }
    private function getDataSource($listNoeud = array(), $niveau, $composant,$axe, $levelname) {
        $datasource  = [];
        foreach ($listNoeud as $listnoeud) {
            $keyregion = '';
            switch (strtoupper($listnoeud['name'])) {
                case 'DAKAR' : $keyregion = "sn-dk";break;
                case 'THIES' : $keyregion = "sn-th";break;
                case 'DIOURBEL' : $keyregion = "sn-db";break;
                case 'KOLDA' : $keyregion = "sn-680";break;
                case 'ZIGUINCHOR' : $keyregion = "sn-zg";break;
                case 'TAMBACOUNDA' : $keyregion = "sn-tc";break;
                case 'SEDHIOU' : $keyregion = "sn-kd";break;
                case 'KEDOUGOU' : $keyregion = "sn-6976";break;
                case 'KAFFRINE' : $keyregion = "sn-6978";break;
                case 'SAINT-LOUIS' : $keyregion = "sn-6975";break;
                case 'FATICK' : $keyregion = "sn-fk";break;
                case 'KAOLACK' : $keyregion = "sn-1181";break;
                case 'LOUGA' : $keyregion = "sn-lg";break;
                case 'MATAM' : $keyregion = "sn-sl";break;
                default : $keyregion = '';break;
            }
            if ($keyregion !== '') {
//                $this->construtDatasource($listNoeud, $listnoeud['name'], $listnoeud['valeurAxe'], $keyregion, $datasource);
                $fils = array();
                $parentaxevalue=0;
                $parentaxevalue2=0;
                foreach ($listNoeud as $list) {
                    if ($list['parent'] !== null && $list['parent'] == $listnoeud['name']) {
                        $isdrilldown = $list['level'] == $niveau ? false : true;
                        $childs = [];

                        if ($levelname == 'Banques') {
                           foreach ($listNoeud as $lst) {
                              if ($lst['parent'] !== null && $lst['parent'] == $list['name']) {
                                 if ($lst['valeurAxe'] > 0) {
                                    if (count($childs) == 0) {
                                       array_push($childs, array(
                                          'id' => $lst['node'],
                                          'name' => $lst['name'],
                                          'parent' => str_replace(" ","",$lst['parent']),
                                          'description' => $lst['description'],
                                          'drilldown' => false,
                                          'y' => $lst['valeurAxe'],
                                          'y2' => $lst['valeurAxe2']
                                       ));
                                    }
                                    else {
                                       $exit = false;
                                       for ($i= 0 ; $i < count($childs) - 1 ; $i++) {
                                          if ($childs[$i]['name'] == $lst['name']) {
                                             $exit = true;
                                             $childs[$i]['y'] += $lst['valeurAxe'];
                                             $childs[$i]['y2'] += $lst['valeurAxe2'];
                                          }
                                       }
                                       if ($exit == false) {
                                          array_push($childs, array(
                                             'id' => $lst['node'],
                                             'name' => $lst['name'],
                                             'parent' => str_replace(" ","",$lst['parent']),
                                             'description' => $lst['description'],
                                             'drilldown' => false,
                                             'y' => $lst['valeurAxe']/1000000,
                                             'y2' => $lst['valeurAxe2']
                                          ));
                                       }
                                    }
                                 }
                              }
                           }
                        }
                       if (count($fils) == 0) {
                          if ($list['valeurAxe'] > 0) {
                             array_push($fils, array(
                                'name' => $list['name'],
                                'id' => str_replace(" ","",$list['name']),
                                'expanded' => false,
                                'description' => $list['description'],
                                'y' =>  $list['valeurAxe'] / 1000000,
                                'y2' => $list['valeurAxe2'] ,
                                'drilldown' => $isdrilldown,
                                'childs' => $childs
                             ));
                          }
                       }else {
                          $exist = false;
                          for ($i=0 ; $i < count($fils) - 1 ; $i++) {
                             if ($fils[$i]['name'] == $list['name']) {
                                $exist = true;
                                $fils[$i]['y'] += $list['valeurAxe'] / 1000000;
                                $fils[$i]['y2'] += $list['valeurAxe2'];
                             }
                          }
                          if ($exist == false) {
                             if ($list['valeurAxe'] > 0) {
                                array_push($fils, array(
                                   'name' => $list['name'],
                                   'id' => str_replace(" ","",$list['name']),
                                   'expanded' => false,
                                   'description' => $list['description'],
                                   'y' =>  $list['valeurAxe'] / 1000000,
                                   'y2' => $list['valeurAxe2'] ,
                                   'drilldown' => $isdrilldown,
                                   'childs' => $childs
                                ));
                             }
                          }
                       }
                       $parentaxevalue += $list['valeurAxe'];
                       $parentaxevalue2 += $list['valeurAxe2'];
                    }
                }

                array_push($datasource, array(
                    'hc_key' => $keyregion,
                    'name' => $listnoeud['name'],
                    'expanded' => false,
                    'description' => $listnoeud['description'],
                    'value' =>  $parentaxevalue / 1000000,
                    'value2' => $parentaxevalue2,
                    'fils' => $fils
                ));
            }
        }
        return $datasource;
    }
    public function getChildren($name) {

    }
    private function construtDatasource($listNoeud, $name, $valeurAxe, $keyregion, &$datasource) {
        $fils = array(); $exist = false;
        try{
            foreach ($listNoeud as $list) {
                if ($list['parent'] !== null && $list['parent'] == $name) {
                    if (count($datasource) > 0) {
                        foreach ($datasource['fils'] as $item) {
                            if ($item['name'] == $name) {
                                $exist = true;
                            }
                        }
                        if ($exist == false) {
                            array_push($fils, array(
                                'name' => $list['name'],
                                'y' => $list['valeurAxe'],
                                'drilldown' => $list['name']
                            ));
                            array_push($datasource, array(
                                'hc_key' => $keyregion,
                                'name' => $name,
                                'value' => $valeurAxe,
                                'fils' => $fils
                            ));
                            $this->construtDatasource($listNoeud,$list['name'], $list['valeurAxe'], $keyregion, $datasource['fils'] );
                        }
                    }


                }
            }
        }catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }


    }

   /**
    * @param Node $node
    * @param $listNoeud
    * @param $valeurAxe
    * @param $secondValeurAxe
    */
    private function constructList(Node $node, &$listNoeud, $valeurAxe,$secondValeurAxe) {
        $exist = false;
        for ($i= 0 ; $i < count($listNoeud) - 1 ; $i++) {
            if (strcasecmp($listNoeud[$i]['node'], $node->getId()) == 0){
                $exist = true;
                $listNoeud[$i]['valeurAxe'] += $valeurAxe;
                $listNoeud[$i]['valeurAxe2'] += $secondValeurAxe;
            }
        }
        if (!$exist) {
            if ($valeurAxe > 0) {
                array_push($listNoeud, array(
                    'node' => $node->getId(),
                    'name' => $node->getName(),
                    'description' => $node->getDescription(),
                    'level' => $node->getLevel(),
                    'parent' => $node->getParent() ? $node->getParent()->getName() : null,
                    'parentId' => $node->getParent() ? $node->getParent()->getId() : null,
                    'valeurAxe' => $valeurAxe,
                    'valeurAxe2' => $secondValeurAxe
                ));
            }

        }
        if ($node->getParent() != null) {
               return $this->constructList($node->getParent(), $listNoeud, $valeurAxe, $secondValeurAxe);
        }
    }
}
