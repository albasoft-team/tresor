<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 14/08/2017
 * Time: 17:55
 */

namespace Vusalba\UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vusalba\UserBundle\Entity\Profile;
use Vusalba\UserBundle\Entity\User;
use Vusalba\UserBundle\Form\ProfileType;

class ProfileController extends Controller
{

    /**
     * @return JsonResponse
     * @Route("/profile/list", name="list_profiles", options={"expose"=true})
     * @Method("GET")
     */
    public function listAction() {
        $profils = $this->getDoctrine()->getManager()
                ->getRepository('UserBundle:Profile')
                ->findAll();

        return new JsonResponse([
            'view' => $this->renderView(':admin/profile:list_profile.html.twig',['profiles' => $profils]),
            'error' => null
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/profile/add", name="profile_add", options={"expose" = true})
     * @Method({"GET","POST"})
     */
    public function addProfile(Request $request) {
        $errors = null;
        $profile = new Profile(User::ROLE_DEFAULT,'Role user par défaut');
        $profile->setRole(null);

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($profile);
            $em->flush();
        }
        $view = $this->renderView(':admin/profile:add_profile.html.twig', ['form' => $form->createView()]);
        return new JsonResponse([
            'view' => $view,
            'error' => $errors,
            'profile' => $profile,
        ]);
    }
}