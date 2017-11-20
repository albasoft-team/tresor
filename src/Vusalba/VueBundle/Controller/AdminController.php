<?php
/**
 * Created by PhpStorm.
 * User: Aly Seck
 * Date: 26/07/2017
 * Time: 14:46
 */

namespace Vusalba\VueBundle\Controller;


use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vusalba\VueBundle\Constante\Constante;
use Vusalba\VueBundle\Constante\Database;


/**
 * Class AdminController
 * @package Vusalba\VueBundle\Controller
 * @Route("administration")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index", options={"expose" =  true})
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $axes = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        $scopes = $em->getRepository('VueBundle:Node')->findAll();
//       var_dump($this->getNewNode());
        return $this->render('admin/index.html.twig', array(
            "axes" => $axes,
            'scopes' => $scopes
        ));
    }

    /**
     * @Route("/genener_table", name="admin_inputtable")
     * @Method("GET")
     */
    public function adminAction() {
        return $this->render('admin/admin.html.twig');
    }
    /**
     * @return JsonResponse
     * @Route("/comp/list", name="list_composant", options={"expose"=true})
     * @Method("GET")
     */
    public function getComposant() {
        $em = $this->getDoctrine()->getManager();
        $composants = $em->getRepository('VueBundle:Composant')->findAll();
        $view = $this->renderView(':composant:index.html.twig',['composants' => $composants]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }
    /**
     * @return JsonResponse
     * @Route("/axe/list", name="list_axe", options={"expose"=true})
     * @Method("GET")
     */
    public function getAxes() {
        $em = $this->getDoctrine()->getManager();
        $axes = $em->getRepository('VueBundle:Axis')->findAll();
        $view = $this->renderView(':axis:index.html.twig',['axes' => $axes]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }
    /**
     * @return JsonResponse
     * @Route("/node/list", name="list_node", options={"expose"=true})
     * @Method("GET")
     */
    public function getNodes() {
        $em = $this->getDoctrine()->getManager();
        $nodes = $em->getRepository('VueBundle:Node')->findAll();
        $view = $this->renderView(':node:index.html.twig',['nodes' => $nodes]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }
     /**
     * @return JsonResponse
     * @Route("/level/list", name="list_level", options={"expose"=true})
     * @Method("GET")
     */
    public function getLevels() {
        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('VueBundle:Level')->findAll();
        $view = $this->renderView(':level:index.html.twig',['levels' => $levels]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/groupe/list", name="list_group", options={"expose"=true})
     * @Method("GET")
     */
    public function getGroupe() {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('VueBundle:Groupe')->findAll();
        $view = $this->renderView(':groupe:index.html.twig',['groupes' => $groups]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }
     /**
     * @return JsonResponse
     * @Route("/axegroupe/list", name="list_axegroup", options={"expose"=true})
     * @Method("GET")
     */
    public function getAxeGroupe() {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('VueBundle:AxeGroupe')->findAll();
        $view = $this->renderView(':axegroupe:index.html.twig',['axeGroupes' => $groups]);

        return new JsonResponse([
            'view' => $view,
            'error' => null
        ]);
    }
     private function getComposants() {
         $em = $this->getDoctrine()->getManager();
         $composants = $em->getRepository('VueBundle:Composant')->findAll();
         return $composants;
     }
     private function getAllNodes() {
         $em = $this->getDoctrine()->getManager();
         $nodes = $em->getRepository('VueBundle:Node')->findAll();

         return $nodes;
     }
     private function getNodeScoped() {
         $em = $this->getDoctrine()->getManager();
         $nodes = $em->getRepository('VueBundle:Node')->findBy(array('isScopeAnalyse' => true));

         return $nodes;
     }
     private function createMatrice(\Doctrine\DBAL\Connection $conn) {
        $comps = $this->getComposants();
        $axes = $this->getAxisColumn();
        $nodes = $this->getAllNodes();
        $tables = [];
        $json = '';
        $id = 1;
        foreach ($nodes as $node) {
            foreach ($comps as $comp) {
                $exist = false;
                if (count($tables) >  0) {
                    foreach ($tables as $table) {
                        if ($comp->getId() == $table->getComposant()->getId()) {
                            $exist = true ;
                        }
                    }
                }
                if ($exist == false) {
                    $json = "{" . '"id"'. ':' . $id . ','.'"compId"'. ':' . $comp->getId() . ',' . '"compName"' . ':"' . $comp->getName() . '","axeValues":[';
                    $i= 0;
                    foreach ($axes as $axe) {
                        if ($i < count($axes) - 1 ) {
                            $json .= '{'
                                .'"name"'.':"' . $axe->getName() . '",'
                                . '"value"' . ':'. 0 .','
                                .'"code"'. ':"'.str_replace(' ','', ucwords($axe->getName())).'",'
                                .'"calculated"'. ':"'.$axe->getIscalculated().'",'
                                .'"formule"'. ':"'. $axe->getFormula()
                                .'"}' .',';
                        }
                        else {
                            $json .= '{'
                                .'"name"' . ':"' . $axe->getName() . '",'
                                . '"value"' . ':' . 0 .','
                                .'"code"'. ':"'.str_replace(' ','', ucwords($axe->getName())).'",'
                                .'"calculated"'. ':"'.$axe->getIscalculated().'",'
                                .'"formule"'. ':"'. $axe->getFormula()
                                .'"}';
                        }
                        $i++;
                    }
                    $json .=']}';
                }
                try{

                    $conn->insert('input_table', array(
                        'composant_id' => $comp->getId(),
                        'tags' => $json,
                        'node_id' => $node->getId()
                    ));
                    $id = $conn->lastInsertId() + 1;
                    if ($id > 1) {
                        $tables = $this->getInputTables();
                    }
                }catch (\Exception $exception){

                }
            }

        }

     }


    /**
     * @Route("/properties", name="admin_properties", options={"expose" = true})
     * @Method("GET")
     * @return string
     *
     */
    public function getProperties() {
        $axes = $this->getAxisColumn();
        $fields = '';
        $i = 0;
        foreach ($axes as $axe) {
            $name = str_replace(' ','', ucwords($axe->getName()));
            if ($i < count($axes) - 1) {
                $fields .= '`'.$name.'`'.' '.'FLOAT'. ',';
            }
            else {
                $fields .= '`'.$name.'`'.' '.'FLOAT';
            }
            $i++;
        }
        return new JsonResponse([
            'fields' => $fields
        ]);
    }
    private function getDistinctInputs() {
        $conn = $this->getConnection();

        $results = Constante::getDistinctInputs($conn);
        return $results;
    }
    private function getDistinctNodes() {
        $conn = $this->getConnection();

        $results = Constante::getDistinctNode($conn);
        return $results;
    }

    private function getNewComposant() {
        $composants = $this->getComposants();
        $tables = $this->getDistinctInputs();
        $arrayResult = [];
        foreach ($composants as $composant) {
            $exist = false;
            foreach ($tables as $table) {
                if ($table['composant_id'] == $composant->getId()) {
                    $exist = true;
                }
            }
            if ($exist == false) {
                array_push($arrayResult, array(
                    'composant_id' => $composant->getId(),
                    'name' => $composant->getName()
                ));
            }
        }

        return $arrayResult;
    }
    private function getNewNode() {
        $nodes = $this->getNodeScoped();
        $tables = $this->getDistinctNodes();
        $arrayResult = [];
        foreach ($nodes as $node) {
            $exist = false;
            foreach ($tables as $table) {
                if ($table['node_id'] == $node->getId()) {
                    $exist = true;
                }
            }
            if ($exist == false) {
//               var_dump($node->getId());
                array_push($arrayResult, array(
                    'node_id' => $node->getId()
                ));
            }
        }
        return $arrayResult;
    }
    private function getNewAxe() {
        $axes = $this->getAxisColumn();
        $tags = json_decode($this->getJson());
        $arrayAxes = [];
        foreach ($axes as $axe) {
            $exist = false;
            foreach ($tags->axeValues as $tag) {
                if ($axe->getName() == $tag->name) {
                    $exist = true;
                }
            }
            if ($exist == false) {
                array_push($arrayAxes, array(
                    'name' => $axe->getName(),
                    'formule' => $axe->getFormula(),
                    'calculated' => $axe->getIscalculated()
                ));
            }
        }
//        var_dump($arrayAxes);
        return $arrayAxes;
    }

    /**
     * @param Request $request
     * @Route("/update", name="update_tags", options={"expose"=true})
     * @Method("POST")
     */
    public function updateInputTable(Request $request) {
       $axes = $this->getAxisColumn();
        $inputtables = $this->getInputTables();
        $newAxes = $this->getNewAxe();
        $newComps = $this->getNewComposant();
        $newNodes = $this->getNewNode();
        $allnodes = $this->getNodeScoped();
        $em = $this->getDoctrine()->getManager();
        if (count($newAxes) > 0) {
            foreach ($inputtables as $inputtable) {
                $jsonTags = json_decode($inputtable->getTags());
                foreach ($newAxes as $newAxe) {
                    $json = '{'
                        . '"name"' . ':"' . $newAxe['name'] . '",'
                        . '"value"' . ':' . 0 . ','
                        . '"code"' . ':"' . str_replace(' ', '', ucwords($newAxe['name'])) . '",'
                        . '"calculated"' . ':"' . $newAxe['calculated'] . '",'
                        . '"formule"' . ':"' . $newAxe['formule']
                        . '"}';
                    array_push($jsonTags->axeValues, json_decode($json));

                }
                $inputtable->setTags(json_encode($jsonTags));
                $em->flush();
            }
        }
        $conn = $this->getConnection();
        if (count($newComps) > 0) {
            foreach ($newComps as $comp) {
                $nodeID = '';
               foreach ($allnodes as $allnode) {
                  $json = $this->getJsonString($axes, $comp);
                  $jsonTags = json_decode($json);
                 if ($nodeID == '') {
                     $table = Constante::getLastInput($conn);
                     foreach ($table as $value) {
                         $jsonTags->id = $value['id'] + 1;
                     }
                 }
                 if ($nodeID !=='' && $nodeID !== $allnode->getId()) {
                     $jsonTags->id = $conn->lastInsertId()+1;
                 }
                 $conn->insert('input_table', array(
                     'composant_id' => $comp['composant_id'],
                     'tags' => json_encode($jsonTags),
                     'node_id' => $allnode->getId()
                 ));
                 $nodeID = $allnode->getId();
                }
            }
        }
        if (count($newNodes) > 0) {
            $comps = $this->getComposants();
            $tags = [];

            foreach ($newNodes as $newNode) {
                $nodeID = '';
                $table = Constante::getLastInput($conn);
                foreach ($table as $value) {
                    $tags = json_decode($value['tags']);
                    $id = $value['id'];
                }
                foreach ($comps as $comp) {
                    $tags->compId = $comp->getId();
                    $tags->compName = $comp->getName();
                    foreach ($tags->axeValues as $value) {
                        $value->value = 0;
                    }
                    if ($nodeID == '') {
                        $tags->id = intval($id)+1;
                    }
                    if ($nodeID !== '') {
                        $tags->id = $conn->lastInsertId() + 1;
                    }
                    $conn->insert('input_table', array(
                        'composant_id' => $comp->getId(),
                        'tags' => json_encode($tags),
                        'node_id' => $newNode['node_id']
                    ));
                    $nodeID = $newNode['node_id'];
                }
            }
        }
//        $results = $em->getRepository('VueBundle:InputTable')->findAll();
//        $serializer = $this->get('serializer');
//
//        $arraResults = $serializer->normalize($results);

        return new JsonResponse((array("statut" => 200)));

    }
    private  function getJsonString($axes, $comp)
    {
       $i = 0;
       $json = "{" . '"id"'. ':' . 0 . ','.'"compId"'. ':' . $comp['composant_id'] . ',' . '"compName"' . ':"' . $comp['name'] . '","axeValues":[';
       foreach ($axes as $axe) {
          if ($i < count($axes) - 1 ) {
             $json .= '{'
                .'"name"'.':"' . $axe->getName() . '",'
                . '"value"' . ':'. 0 .','
                .'"code"'. ':"'.str_replace(' ','', ucwords($axe->getName())).'",'
                .'"calculated"'. ':"'.$axe->getIscalculated().'",'
                .'"formule"'. ':"'. $axe->getFormula()
                .'"}' .',';
          }
          else {
             $json .= '{'
                .'"name"' . ':"' . $axe->getName() . '",'
                . '"value"' . ':' . 0 .','
                .'"code"'. ':"'.str_replace(' ','', ucwords($axe->getName())).'",'
                .'"calculated"'. ':"'.$axe->getIscalculated().'",'
                .'"formule"'. ':"'. $axe->getFormula()
                .'"}';
          }
          $i++;
       }
       $json .=']}';
       return $json;
    }
    private function getJson() {
        $em = $this->getDoctrine()->getManager();
        $first = $em->getRepository('VueBundle:InputTable')->findOneElement();

        $allAxes = $first[0]->getTags();
        return $allAxes;
    }
    private function getInputTables() {
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('VueBundle:InputTable')->findAll();
        return $results;
    }
    /**
     * @param Request $request
     * @Route("/entity", name="create_entity", options={"expose" = true})
     */
    public function createEntity(Request $request) {
        $axes =$this->getAxisColumn();
        $fields = '';
        $i = 0;
        $rep = false;
        $errors = '';
        foreach ($axes as $axe) {
            $name = str_replace(' ','', ucwords($axe->getName()));
            if ($i < count($axes) - 1) {
                $fields .= '`'.$name.'`'.' '.'FLOAT NULL'. ',';
            }
            else {
                $fields .= '`'.$name.'`'.' '.'FLOAT NULL';
            }
            $i++;
        }
        $conn = $this->getConnection();

        $query = Constante::getCreateQuery($fields);
        $statement = $conn->prepare($query);
        try {
                $statement->execute();

                Constante::$table_created = 1 ;
                $args = Constante::MAPPING_IMPORT;
                $cache = Constante::CACHE_ENV_PROD;
              //  $this->longTaskAction();
                $cache_path = $this->get('kernel')->getCacheDir(). '/../..';
//                $cache_resp = $this->runCommande($cache);
//                chmod($cache_path, 777);

                $response = $this->runCommande($args);
//                $cache_resp = $this->runCommande($cache);
//                chmod($cache_path, 777);
//            if ($response !== '') {
//                $path = Constante::ORM_PATH;
//                $supp = $this->deleteDirectory($path);
//            }

            $this->createMatrice($conn);
        }catch (\Exception $exception) {
            $errors = $exception->getMessage();
        }

        return new JsonResponse([
            'result' => $response,
            'error' => $errors
        ]);
    }

    /**
     * @Route("/entities", name="generate_all", options={"expose" = true})
     * @Method("POST")
     */
    public function generateEntities() {
        $argsEntities = Constante::GENERATE_ENTITIES;
        $entities = $this->runCommande($argsEntities);

        return new JsonResponse([
            'result' => $entities
        ]);
    }
    /**
     * Delete doctrine directory
     * @param $dir
     * @return bool
     */
    private function deleteDirectory($dir) {
        if (!file_exists($dir)) { return true; }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        $dir .='/';
        $files = $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $item) {
            if (is_file($item)){unlink($item);}
        }
        return rmdir($dir);
    }
    private function runCommande($args) {
        try {
            $kernel = $this->get('kernel');
            $app = new Application($kernel);
            $input = new ArrayInput($args);
            $output = new NullOutput();
            $app->doRun($input, $output);
            $responses = 'Succefully';
        }catch (\Exception $exception) {
            $responses = $exception->getMessage();
        }

        return $responses;
    }
    private function getConnection() {
       $conn =  $this->getDoctrine()->getConnection();

       return $conn;
    }
    public function longTaskAction()
    {
        set_time_limit(0) ;// 0 = no limits
        // ..
    }
    private function getAxisColumn()
    {
        $em = $this->getDoctrine()->getManager();
        $axes = $em->getRepository('VueBundle:Axis')->findAll();
        return $axes;
    }

}