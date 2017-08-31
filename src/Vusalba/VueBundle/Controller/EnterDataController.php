<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 08/08/2017
 * Time: 11:26
 */

namespace Vusalba\VueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vusalba\VueBundle\Entity\Inputtable;

/**
 * Class EnterDataController
 * @package Vusalba\VueBundle\Controller
 * @Route("enterData")
 */
class EnterDataController extends Controller
{

    /**
     *
     * @Route("/", name="enter_index", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction () {
        $em = $this->getDoctrine()->getManager();
        $axes = $em->getRepository('VueBundle:Axis')->findAll();
        return $this->render(':EnterData:index.html.twig',['axes' => $axes]);
    }

    /**
     * @return JsonResponse
     * @Route("/all", name="enter_data", options={"expose" = true})
     * @Method("GET")
     */
    public function getAll() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $inputtales = [];
        if ($user->getNode()) {
            $inputtales = $em->getRepository('VueBundle:InputTable')->findBy(array(
                'node' => $user->getNode()
            ));
        }
        $serializer = $this->get('serializer');
        $arrayResult = $serializer->normalize($inputtales);

        return new JsonResponse($arrayResult);
    }

    /**
     * @return JsonResponse
     * @Route("/edit", name="edit_data", options={"expose"=true})
     * @Method("POST")
     */
    public function editData(Request $request) {
        $data = json_decode($request->getContent(), true);
        $tags = $request->getContent();
        $id = $data['id'];
        $em = $this->getDoctrine()->getManager();

        $inputTable = $em->getRepository('VueBundle:InputTable')->find($id);
        $inputTable->setTags($tags);
        $em->flush();

        $arrResult = $this->getAll();

        return $arrResult;

    }
}