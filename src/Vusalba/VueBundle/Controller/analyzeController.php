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
     * @Route("/analyser", name="postdata_analyse", options={"expose" = true})
     * @Method("POST")
     */
    public function postDataAnalyse(Request $request) {
        $data = json_decode($request->getContent(), TRUE);
        $composant =  explode('|', $data['composant'])[0];
        $axe = explode('|',$data['axe'])[0];
        $niveau = $data['level'];

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

       // $listNoeuds = [];
       foreach ($arrayResults as $result) {
           $node = $em->getRepository('VueBundle:Node')->find($result['node']);
           $jsonobject = json_decode($result['tags']);
           $valeurAxe = 0;
         //  var_dump($result);
            foreach ($jsonobject->axeValues as $axeValue ) {
            //   var_dump($axeValue);
                if ($axeValue->code == $axe) {
                    $valeurAxe = str_replace(" ","", $axeValue->value);
                }
            }
            $this->constructList($node, $listNoeud, $valeurAxe);
//            $this->getResultData($listNoeud);
//bricolage
          for ($i= 0 ; $i < count($listNoeud) - 1 ; $i++) {
               for ($j= 0 ; $j < count($listNoeud) - 1 ; $j++) {
                   if ($i!=$j) {
                       if ($listNoeud[$i]['node'] == $listNoeud[$j]['node']) {
                           $listNoeud[$i]['valeurAxe'] += $listNoeud[$j]['valeurAxe'];
                           $listNoeud[$j]['valeurAxe']=0;
                       }
                   }
               }
           }
       }
        $response = $this->getDataSource($listNoeud, $niveau, $composant,$axe);
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
    private function getDataSource($listNoeud = array(), $niveau, $composant,$axe) {
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
                foreach ($listNoeud as $list) {
                    if ($list['parent'] !== null && $list['parent'] == $listnoeud['name']) {
                        $isdrilldown = $list['level'] == $niveau ? false : true;
                        if ($list['valeurAxe'] > 0) {
                        array_push($fils, array(
                            'name' => $list['name'],
                            'y' => ($axe == 'NombreTotal') ? intval($list['valeurAxe']) : $list['valeurAxe'] / 1000000,
                            'drilldown' => $isdrilldown
                        ));
                        $parentaxevalue += $list['valeurAxe'];
                    }
                    }
                }

                array_push($datasource, array(
                    'hc_key' => $keyregion,
                    'name' => $listnoeud['name'],
                    'value' => ($axe=='NombreTotal')?intval($parentaxevalue): $parentaxevalue / 1000000,
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
    private function constructList(Node $node, &$listNoeud, $valeurAxe) {
        $exist = false;
        for ($i= 0 ; $i < count($listNoeud) - 1 ; $i++) {
            if ($listNoeud[$i]['node'] == $node->getId()){
                $exist = true;
                $listNoeud[$i]['valeurAxe'] += $valeurAxe;
            }
        }
        if ($exist == false) {
            if ($valeurAxe > 0) {
                array_push($listNoeud, array(
                    'node' => $node->getId(),
                    'name' => $node->getName(),
                    'level' => $node->getLevel(),
                    'parent' => $node->getParent() ? $node->getParent()->getName() : null,
                    'valeurAxe' => $valeurAxe
                ));
            }

        }
        if ($node->getParent() !== null) {
                $this->constructList($node->getParent(), $listNoeud, $valeurAxe);
        }
    }
}
