<?php

namespace TRando\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TRandoMainBundle:Default:index.html.twig');
    }
}
