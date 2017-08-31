<?php

namespace Vusalba\VueBundle\Controller;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vusalba\VueBundle\Entity\Composant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Composant controller.
 *
 * @Route("composant")
 */
class ComposantController extends Controller
{
    /**
     * Lists all composant entities.
     *
     * @Route("/", name="composant_index", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $composants = $em->getRepository('VueBundle:Composant')->findAll();

        return $this->render('composant/index.html.twig', array(
            'composants' => $composants,
        ));
    }


    /**
     * @return JsonResponse
     * @Route("/allComp", name="all_comp", options={"expose" = true})
     * @Method("GET")
     */
    public function getComposants() {
        $em = $this->getDoctrine()->getManager();
        $composants = $em->getRepository('VueBundle:Composant')->findAll();
        $serializer = $this->get('serializer');
        $arrayResult = $serializer->normalize($composants);

        return new JsonResponse($arrayResult);

    }
    /**
     * @Route("/composant/list", name="composant_list", options={"expose" = true})
     * @Method("GET")
     */
    public function listComposant() {
        $em = $this->getDoctrine()->getManager();

        $composants = $em->getRepository('VueBundle:Composant')->findAll();

        $view = $this->renderView(':admin:composant.html.twig',['composants' => $composants]);

        return new JsonResponse([
            'view' => $view,
            'error' =>null
        ]);
    }
    /**
     * Creates a new composant entity.
     *
     * @Route("/add", name="composant_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $composant = new Composant();
        $errors = null;
        $form = $this->createForm('Vusalba\VueBundle\Form\ComposantType', $composant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($composant);
            $em->flush();
//            return $this->redirectToRoute('composant_show', array('id' => $composant->getId()));
        }
        elseif ($request->getMethod() == "POST") {
            $errors = "Le formulaire est invalide";
        }
        $view = $this->renderView("composant/new.html.twig", ['form' => $form->createView()]);
        return new JsonResponse([
            'view' => $view,
            'error' => $errors
        ]);
    }

    /**
     * Finds and displays a composant entity.
     *
     * @Route("/{id}", name="composant_show")
     * @Method("GET")
     */
    public function showAction(Composant $composant)
    {
        $deleteForm = $this->createDeleteForm($composant);

        return $this->render('composant/show.html.twig', array(
            'composant' => $composant,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing composant entity.
     *
     * @Route("/{id}/edit", name="composant_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Composant $composant)
    {
        $deleteForm = $this->createDeleteForm($composant);
        $editForm = $this->createForm('Vusalba\VueBundle\Form\ComposantType', $composant);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('composant_edit', array('id' => $composant->getId()));
        }

        return $this->render('composant/edit.html.twig', array(
            'composant' => $composant,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a composant entity.
     *
     * @Route("/{id}", name="composant_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Composant $composant)
    {
        $form = $this->createDeleteForm($composant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($composant);
            $em->flush();
        }

        return $this->redirectToRoute('composant_index');
    }

    /**
     * Creates a form to delete a composant entity.
     *
     * @param Composant $composant The composant entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Composant $composant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('composant_delete', array('id' => $composant->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
