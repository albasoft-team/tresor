<?php

namespace Vusalba\VueBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Vusalba\VueBundle\Entity\Level;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Level controller.
 *
 * @Route("level")
 */
class LevelController extends Controller
{
    /**
     * Lists all level entities.
     *
     * @Route("/", name="level_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $levels = $em->getRepository('VueBundle:Level')->findAll();

        return $this->render('level/index.html.twig', array(
            'levels' => $levels,
        ));
    }

    /**
     * Creates a new level entity.
     *
     * @Route("/new", name="level_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $level = new Level();
        $form = $this->createForm('Vusalba\VueBundle\Form\LevelType', $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($level);
            $em->flush();

            return $this->redirectToRoute('level_show', array('id' => $level->getId()));
        }

        return $this->render('level/new.html.twig', array(
            'level' => $level,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a level entity.
     *
     * @Route("/{id}", name="level_show")
     * @Method("GET")
     */
    public function showAction(Level $level)
    {
        $deleteForm = $this->createDeleteForm($level);

        return $this->render('level/show.html.twig', array(
            'level' => $level,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing level entity.
     *
     * @Route("/{id}/edit", name="level_edit" , options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
//        $deleteForm = $this->createDeleteForm($level);
        $errors = null;
        $level = $this->findLevelById($id);
        $editForm = $this->createForm('Vusalba\VueBundle\Form\LevelType', $level);
        $editForm->handleRequest($request);
        $niveau = $level->getNiveau();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $nodes = $em->getRepository('VueBundle:Node')->findBy(array('level' => $niveau));
                foreach ($nodes as $node) {
                    $node->setIsScopeAnalyse($level->getScopeAnalysis());
                    $em->flush();
                }


            $this->getDoctrine()->getManager()->flush();
        }
        $view =  $this->renderView(':level:edit.html.twig', ['form' => $editForm->createView(),'id' =>$id , 'level' => $niveau]);
        return New JsonResponse([
            'view' => $view,
            'error' => $errors
        ]);
    }

    /**
     * Deletes a level entity.
     *
     * @Route("/{id}", name="level_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Level $level)
    {
        $form = $this->createDeleteForm($level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($level);
            $em->flush();
        }

        return $this->redirectToRoute('level_index');
    }

    /**
     * Creates a form to delete a level entity.
     *
     * @param Level $level The level entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Level $level)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('level_delete', array('id' => $level->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function findLevelById($id)
    {
        $level = $this->getDoctrine()
            ->getManager()
            ->getRepository('VueBundle:Level')
            ->find($id);
        return $level;
    }
}
