<?php

namespace Vusalba\VueBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vusalba\VueBundle\Entity\Level;
use Vusalba\VueBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Node controller.
 *
 * @Route("node")
 */
class NodeController extends Controller
{
    /**
     * Lists all node entities.
     *
     * @Route("/", name="node_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $nodes = $em->getRepository('VueBundle:Node')->findAll();

        return $this->render('node/index.html.twig', array(
            'nodes' => $nodes,
        ));
    }

    /**
     * @Route("/list", name="node_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getListScope() {
        $em = $this->getDoctrine()->getManager();
        $scopes = $em->getRepository('VueBundle:Node')->findAll();

        return new JsonResponse([
            'view' => $this->renderView(':admin:scope.html.twig', ['scopes' => $scopes]),
            'error' =>null
        ]);
    }
    /**
     * Creates a new node entity.
     *
     * @Route("/new", name="node_new", options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $node = new Node();
        $error = null;
        $form = $this->createForm('Vusalba\VueBundle\Form\NodeType', $node);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $level =  $em->getRepository('VueBundle:Level');
            $node->setIsScopeAnalyse(false);
            if (!$this->isLevelExist())
            {
                $node->setParent(null);
                $node->setLevel(0);
                $this->persistNode($em,$node);
               $level->createLevel('Level0',false,0);
            }
            else {
                $parent_level = 0;
                if ($node->getParent() !== null) {
                    $parent_level = $this->getParentLevel($node->getParent());
                }
                $levels = $level->getLevel($parent_level);
                if ($levels) {
                    $node->setLevel($parent_level + 1);
                    $this->persistNode($em, $node);
                }
                else {
                    $level->createLevel('Level'.($parent_level + 1) ,false,$parent_level + 1);
                    $node->setLevel($parent_level+1);
                    $this->persistNode($em, $node);
                }
            }
            $lastNode = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('VueBundle:Node')->findBy([],['id' => 'DESC'],['limit' => 1]);
            $listLevels = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('VueBundle:Level')->findAll();
            foreach ($listLevels as $listLevel) {
                foreach ($lastNode as $last) {
                    if ($listLevel->getNiveau() == $last->getLevel() && $listLevel->getScopeAnalysis() ==true) {
                        $last->setIsScopeAnalyse(true);
                        $this->getDoctrine()->getManager()->flush();
                    }
                }
            }
        }elseif ($request->getMethod() == 'POST') {
            $error = "Le formulaire n'est pas valide ";
        }
        $view = $this->renderView(':node:new.html.twig', ['form' => $form->createView()]);

        return new JsonResponse([
            'view' => $view,
            'error' => $error
        ]);
    }

    /**
     * Finds and displays a node entity.
     *
     * @Route("/{id}", name="node_show")
     * @Method("GET")
     */
    public function showAction(Node $node)
    {
        $deleteForm = $this->createDeleteForm($node);

        return $this->render('node/show.html.twig', array(
            'node' => $node,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing node entity.
     *
     * @Route("/{id}/edit", name="node_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
//        $deleteForm = $this->createDeleteForm($node);
        $errors = null;
        $node = $this->findNodeById($id);
        $editForm = $this->createForm('Vusalba\VueBundle\Form\NodeType', $node);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();
        }
        $view =  $this->renderView(':node:edit.html.twig', ['form' => $editForm->createView(), 'id'=>$id]);
        return New JsonResponse([
            'view' => $view,
            'error' => $errors
        ]);
    }

    private function findNodeById($id) {
        $node = $this->getDoctrine()
            ->getManager()
            ->getRepository('VueBundle:Node')
            ->find($id);
        return $node;
    }
    /**
     * Deletes a node entity.
     *
     * @Route("/{id}", name="node_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Node $node)
    {
        $form = $this->createDeleteForm($node);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($node);
            $em->flush();
        }

        return $this->redirectToRoute('node_index');
    }

    /**
     * Creates a form to delete a node entity.
     *
     * @param Node $node The node entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Node $node)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('node_delete', array('id' => $node->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function isLevelExist()
    {
        $em = $this->getDoctrine()->getManager();
        $levels = $em->getRepository('VueBundle:Level')->findAll();
        return count($levels) > 0 ? true : false;
    }

    private function persistNode(ObjectManager $em, $node)
    {
        $em->persist($node);
        $em->flush();
    }

    private function getParentLevel(Node $parent) {
        return $parent->getLevel();
    }
}
