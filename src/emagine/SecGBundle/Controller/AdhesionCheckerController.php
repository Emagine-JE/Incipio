<?php

namespace emagine\SecGBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use emagine\SecGBundle\Entity\AdhesionCheckerCategory;
use emagine\SecGBundle\Form\AdhesionCheckerCategoryType;

use mgate\PersonneBundle\Entity\Membre;

class AdhesionCheckerController extends Controller
{
    //TODO manque d'optimisation d'an l'affichage de l'adhesionChecker
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

   //AJAX
   public function checkAction(Request $request){
        if($request->isXmlHttpRequest()){

            //TT
            //$membreid = $request->query->get("membreid");
            //$categoryid = $request->query->get("categoryid");
            
            $membreid = $request->request->get("membreid");
            $categoryid = $request->request->get("categoryid");

            $em = $this->getDoctrine()->getManager();
            $repositoryM = $em->getRepository('mgatePersonneBundle:Membre');
            $repositoryA = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory');


            $membre = $repositoryM->find($membreid);
            $adhesion = $repositoryA->find($categoryid);

            if(!$membre->getAdhesions()->contains($adhesion)){
                $membre->addAdhesion($adhesion);

                $em->persist($membre);
                $em->flush();
            }else{
                $em->remove($result);
                $em->flush();
            }

            //TT
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);

            return new Response();
        }else{
            //throw $this->createNotFoundException('Vous ne pouvez pas acceder à cette page');
        }
   }
}
