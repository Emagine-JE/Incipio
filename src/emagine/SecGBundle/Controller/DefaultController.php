<?php

namespace emagine\SecGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('emagineSecGBundle:Default:index.html.twig');
    }
}
