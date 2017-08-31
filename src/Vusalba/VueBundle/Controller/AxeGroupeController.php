<?php

namespace Vusalba\VueBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Vusalba\VueBundle\Entity\AxeGroupe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Axegroupe controller.
 *
 * @Route("axegroupe")
 */
class AxeGroupeController extends Controller
{
    /**
     * Lists all axeGroupe entities.
     *
     * @Route("/", name="axegroupe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $axeGroupes = $em->getRepository('VueBundle:AxeGroupe')->findAll();

        return $this->render('axegroupe/index.html.twig', array(
            'axeGroupes' => $axeGroupes,
        ));
    }

    /**
     * @return JsonResponse
     * @Route("/list", name="axegroupe_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getListAxeGroupe() {
        $em = $this->getDoctrine()->getManager();
        $axegroups = $em->getRepository('VueBundle:AxeGroupe')->findAll();
        return new  JsonResponse([
           'view' => $this->renderView(':admin:axegroup.html.twig', ['axegroupes' => $axegroups]),
           'error' => null
        ]);
    }
    /**
     * Creates a new axeGroupe entity.
     *
     * @Route("/new", name="axegroupe_new", options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $axeGroupe = new Axegroupe();
        $errors =  null;
        $form = $this->createForm('Vusalba\VueBundle\Form\AxeGroupeType', $axeGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($axeGroupe);
            $em->flush();
        } elseif ($request->getMethod() == 'POST') {
            $errors = "Le formulaire n'est pas valide !!!";
        }
        $view = $this->renderView(':axegroupe:new.html.twig', ['form' => $form->createView()]);

        return new JsonResponse([
            'view' => $view,
            'error' => $errors
        ]);

    }

    /**
     * Finds and displays a axeGroupe entity.
     *
     * @Route("/{id}", name="axegroupe_show")
     * @Method("GET")
     */
    public function showAction(AxeGroupe $axeGroupe)
    {
        $deleteForm = $this->createDeleteForm($axeGroupe);

        return $this->render('axegroupe/show.html.twig', array(
            'axeGroupe' => $axeGroupe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing axeGroupe entity.
     *
     * @Route("/{id}/edit", name="axegroupe_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AxeGroupe $axeGroupe)
    {
        $deleteForm = $this->createDeleteForm($axeGroupe);
        $editForm = $this->createForm('Vusalba\VueBundle\Form\AxeGroupeType', $axeGroupe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('axegroupe_edit', array('id' => $axeGroupe->getId()));
        }

        return $this->render('axegroupe/edit.html.twig', array(
            'axeGroupe' => $axeGroupe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a axeGroupe entity.
     *
     * @Route("/{id}", name="axegroupe_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AxeGroupe $axeGroupe)
    {
        $form = $this->createDeleteForm($axeGroupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($axeGroupe);
            $em->flush();
        }

        return $this->redirectToRoute('axegroupe_index');
    }

    /**
     * Creates a form to delete a axeGroupe entity.
     *
     * @param AxeGroupe $axeGroupe The axeGroupe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AxeGroupe $axeGroupe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('axegroupe_delete', array('id' => $axeGroupe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
