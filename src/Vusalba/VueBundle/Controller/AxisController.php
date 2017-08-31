<?php

namespace Vusalba\VueBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Vusalba\VueBundle\Entity\Axis;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Vusalba\VueBundle\Form\AxisType;

/**
 * Axi controller.
 *
 * @Route("axis")
 */
class AxisController extends Controller
{
    /**
     * Lists all axi entities.
     *
     * @Route("/", name="axis_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $axes = $em->getRepository('VueBundle:Axis')->findAll();

        return $this->render('axis/index.html.twig', array(
            'axes' => $axes,
        ));
    }
    /**
 * @Route("/allAxis", name="all_axis", options={"expose" = true})
 * @Method("GET")
 */
    public function listAxes() {
        $em = $this->getDoctrine()->getManager();

        $composants = $em->getRepository('VueBundle:Axis')->findAll();
        $serializer = $this->get('serializer');
        $arrayResult = $serializer->normalize($composants);
        return new JsonResponse($arrayResult);
    }
    /**
     * @Route("/axe/lists", name="axis_lists", options={"expose" = true})
     * @Method("GET")
     */
    public function listAxis() {
        $em = $this->getDoctrine()->getManager();

        $axes = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        return new JsonResponse([
            'view' => $this->renderView(':admin:axe.html.twig', ['axes' => $axes]),
            'error' =>null
        ]);
    }
    /**
     * @Route("/axe/list", name="axis_list", options={"expose" = true})
     * @Method("GET")
     */
    public function listOfAxis() {
        $em = $this->getDoctrine()->getManager();

        $axes = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        return new JsonResponse([
            'view' => $this->renderView(':axis:axeselected.html.twig', ['axes' => $axes]),
            'opera' => $this->renderView(':axis:operande2.html.twig', ['axes' => $axes]),
            'error' =>null
        ]);
    }
    /**
     * @Route("/axe/operand2", name="op_list", options={"expose" = true})
     * @Method("GET")
     */
    public function listOfAxis2() {
        $em = $this->getDoctrine()->getManager();

        $axes = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        return new JsonResponse([
            'view' => $this->renderView(':axis:operande2.html.twig', ['axes' => $axes]),
            'error' =>null
        ]);
    }


    /**
     * Creates a new axi entity.
     *
     * @Route("/new", name="axis_new", options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $axi = new Axis();
        $errors = null; $allAxis = [];
        $form = $this->createForm(AxisType::class, $axi);
        $form->handleRequest($request);
        try {
            $em = $this->getDoctrine()->getManager();
            if ($form->isSubmitted() && $form->isValid()) {
                $axi->setCode(str_replace(' ','', ucwords($axi->getName())));
                $em->persist($axi);
                $em->flush();

//                return $this->redirectToRoute('analyze_index');
            }
            elseif ($request->getMethod() == 'POST') {
                $errors = "Le formulaire n'est pas valide !!!";
            }
            $allAxis = $em->getRepository('VueBundle:Axis')->findBy(['iscalculated' => false]);
        }catch (\Exception $exception) {
            $errors = $exception->getFile();
        }
        $view = $this->renderView("axis/new.html.twig", ['form' => $form->createView()]);
        return new JsonResponse([
            'view' => $view,
            'error' => $errors,
            'axis' => $allAxis
        ]);
    }

    /**
     * Finds and displays a axi entity.
     *
     * @Route("/{id}", name="axis_show")
     * @Method("GET")
     */
    public function showAction(Axis $axi)
    {
        $deleteForm = $this->createDeleteForm($axi);

        return $this->render('axis/show.html.twig', array(
            'axi' => $axi,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing axi entity.
     *
     * @Route("/{id}/edit", name="axis_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
//        $deleteForm = $this->createDeleteForm($axi);
        $axi = $this->findAxeById($id);
        $errors = null;
        try{
            $editForm = $this->createForm('Vusalba\VueBundle\Form\AxisType', $axi);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();
            }
            $view =  $this->renderView(':axis:edit.html.twig', ['form' => $editForm->createView(), 'id'=>$id]);
            return New JsonResponse([
                'view' => $view,
                'error' => null
            ]);
        }catch (\Exception $exception) {
            $errors = $exception->getMessage(). $exception->getLine() . $exception->getFile();
        }
       return New JsonResponse([
            'error' => $errors
        ]);

//        return $this->render('axis/edit.html.twig', array(
//            'axi' => $axi,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
    }

    /**
     * Deletes a axi entity.
     *
     * @Route("/{id}", name="axis_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Axis $axi)
    {
        $form = $this->createDeleteForm($axi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($axi);
            $em->flush();
        }

        return $this->redirectToRoute('axis_index');
    }

    /**
     * Creates a form to delete a axi entity.
     *
     * @param Axis $axi The axi entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Axis $axi)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('axis_delete', array('id' => $axi->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function findAxeById($id)
    {
        $axe = $this->getDoctrine()
            ->getManager()
            ->getRepository('VueBundle:Axis')
            ->find($id);
        return $axe;
    }
}
