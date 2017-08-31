<?php

namespace Vusalba\VueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('VueBundle:Default:index.html.twig');
    }
}
