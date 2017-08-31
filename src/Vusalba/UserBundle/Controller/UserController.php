<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 14/08/2017
 * Time: 16:09
 */

namespace Vusalba\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vusalba\UserBundle\Entity\Profile;
use Vusalba\UserBundle\Entity\User;
use Vusalba\UserBundle\Form\UserType;

/**
 * Class UserController
 * @package Vusalba\UserBundle\Controller
 * @Route("user")
 */
class UserController extends \FOS\UserBundle\Controller\SecurityController
{
    /**
     * @Route("/", name="user_index", options={"expose"=true})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() {
        return $this->render(':admin/user:index.html.twig');
    }

    /**
     * @Route("/list", name="list_users", options={"expose"=true})
     * @return JsonResponse
     */
    public function allUsers() {
        $users = $this->get('fos_user.user_manager')->findUsers();

        return new JsonResponse([
            'view' => $this->renderView(':admin/user:list_users.html.twig',['users' => $users]),
            'error' => null
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/add", name="user_add", options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function addUserAction(Request $request) {
        $user = new User();
        $error = null;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setEnabled(true);

            $profile = $em->getRepository('UserBundle:Profile')->find(2);
            $user->setRoles([$profile]);
            $em->persist($user);
            $em->flush();
        } elseif ($request->getMethod() == 'POST') {
            $error = 'Le formulaire est invalide';
        }
        $view = $this->renderView(':admin/user:add.html.twig', ['form' => $form->createView()]);

        return new JsonResponse([
           'view' => $view,
            'error' => $error,
            'user' => $user
        ]);

    }

}