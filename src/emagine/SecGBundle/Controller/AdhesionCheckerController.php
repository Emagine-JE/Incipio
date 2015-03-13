<?php

namespace emagine\SecGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use emagine\SecGBundle\Entity\AdhesionCheckerCategory;

class AdhesionCheckerController extends Controller
{
    public function indexAction()
    {


        return $this->render('emagineSecGBundle:AdhesionChecker:index.html.twig', array(
                
        ));    
    }

    public function addCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')->findAll();

        return $this->render('emagineSecGBundle:AdhesionChecker:addCategory.html.twig', array(
            "categories" => $categories,
        )); 
   }

   public function showCategory(){
        return $this->render('emagineSecGBundle:AdhesionChecker:addCategory.html.twig', array(
                // ...
            ));    
   }

}
