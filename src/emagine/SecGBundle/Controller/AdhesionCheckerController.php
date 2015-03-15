<?php

namespace emagine\SecGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use emagine\SecGBundle\Entity\AdhesionCheckerCategory;
use emagine\SecGBundle\Form\AdhesionCheckerCategoryType;

use mgate\PersonneBundle\Entity\Membre;

class AdhesionCheckerController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')->findAll();
        $membres = $em->getRepository('mgatePersonneBundle:Membre')->getCotisants();

        return $this->render('emagineSecGBundle:AdhesionChecker:index.html.twig', array(
               "categories" => $categories,
               "membres"    => $membres,
        ));    
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')->findAll();

        // On récupère le service
        $security = $this->get('security.context');
        // On récupère le token
        $token = $security->getToken();
        // on récupère l'utilisateur
        $user = $token->getUser();

        $category = new AdhesionCheckerCategory();

        $form = $this->createForm(new AdhesionCheckerCategoryType(), $category);

        if( $this->get('request')->getMethod() == 'POST' ){
            $form->bind($this->get('request'));
               
            if( $form->isValid() ){

                $em->persist($category);    
                $em->flush();
    
                return $this->redirect( $this->generateUrl('emagine_secG_AdhesionChecker_show_category', array('id' => $category->getId())) );
            }
        }

        return $this->render('emagineSecGBundle:AdhesionChecker:addCategory.html.twig', array(
            "form"       => $form->createView(),
            "categories" => $categories,
        )); 
   }

   /**
    * @Secure(roles="ROLE_CA")
   */
   public function showCategoryAction($id){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')->find($id);

        return $this->render('emagineSecGBundle:AdhesionChecker:showCategory.html.twig', array(
                "category" => $category
            ));    
   }

    /**
    * @Secure(roles="ROLE_CA")
   */
   public function updateCategoryAction($id){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')->find($id);

        $form = $this->createForm(new AdhesionCheckerCategoryType(), $category);

        if( $this->get('request')->getMethod() == 'POST' ){
            $form->bind($this->get('request'));
               
            if( $form->isValid() ){

                $em->persist($category);    
                $em->flush();
    
                return $this->redirect( $this->generateUrl('emagine_secG_AdhesionChecker_show_category', array('id' => $category->getId())) );
            }
        }

        return $this->render('emagineSecGBundle:AdhesionChecker:updateCategory.html.twig', array(
                "form"       => $form->createView(),
                "category" => $category
            ));    
   }

}
