<?php

namespace emagine\ComBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('emagineComBundle:Default:index.html.twig');
    }

    public function charteGraphiqueAction(){
    	return $this->render('emagineComBundle:Default:charte.html.twig');
    }
}
