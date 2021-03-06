<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\DocTypeSuiviType;
use mgate\SuiviBundle\Form\SuiviEtudeType;

//use mgate\UserBundle\Entity\User;

define("STATE_ID_EN_NEGOCIATION", 1);
define("STATE_ID_EN_COURS", 2);
define("STATE_ID_EN_PAUSE", 3);
define("STATE_ID_TERMINEE", 4);
define("STATE_ID_AVORTEE", 5);

class EtudeController extends Controller {


    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page) {
        $MANDAT_MAX = $this->get('mgate.etude_manager')->getMaxMandat();

        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.context')->getToken()->getUser()->getPersonne();

        //Etudes En Négociation : stateID = 1
        $etudesEnNegociation = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_NEGOCIATION), array('mandat' => 'DESC', 'num' => 'DESC'));

        //Etudes En Cours : stateID = 2
        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_COURS), array('mandat' => 'DESC', 'num' => 'DESC'));

        //Etudes en pause : stateID = 3
        $etudesEnPause = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_PAUSE), array('mandat' => 'DESC', 'num' => 'DESC'));

        //Etudes Terminees et Avortees Chargée en Ajax dans getEtudesAsyncAction
        //On push des arrays vides pour avoir les menus déroulants
        $etudesTermineesParMandat = array();
        $etudesAvorteesParMandat = array();

        for ($i = 1; $i <= $MANDAT_MAX; $i++) {
            array_push($etudesTermineesParMandat, array());
            array_push($etudesAvorteesParMandat, array());
        }


        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
                    'etudesEnNegociation' => $etudesEnNegociation,
                    'etudesEnCours' => $etudesEnCours,
                    'etudesEnPause' => $etudesEnPause,
                    'etudesTermineesParMandat' => $etudesTermineesParMandat,
                    'etudesAvorteesParMandat' => $etudesAvorteesParMandat,
        ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function getEtudesAsyncAction() {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if ($request->getMethod() == 'GET') {
            
            $mandat = $request->query->get('mandat');
            $stateID = $request->query->get('stateID'); 

            // CHECK VAR ATTENTION INJECTION SQL ?
            $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => $stateID, 'mandat' => $mandat), array('num' => 'DESC'));

            if ($stateID == STATE_ID_TERMINEE)
                return $this->render('mgateSuiviBundle:Etude:Tab/EtudesTerminees.html.twig', array('etudes' => $etudes,));
            else if ($stateID == STATE_ID_AVORTEE)
                return $this->render('mgateSuiviBundle:Etude:Tab/EtudesAvortees.html.twig', array('etudes' => $etudes,));
        }
        else
             return $this->render('mgateSuiviBundle:Etude:Tab/EtudesAvortees.html.twig', array(
                        'etudes' => null,
                    ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function stateAction() {

        $em = $this->getDoctrine()->getManager();

        $stateDescription = isset($_POST['state']) ? $_POST['state'] : "";
        $stateID = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $etudeID = isset($_POST['etude']) ? intval($_POST['etude']) : 0;

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($etudeID)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        } else {

            $etude->setStateDescription($stateDescription);
            $etude->setStateID($stateID);
            $em->persist($etude);
            $em->flush();
        }
        return new Response('ok !');
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction() {
        $etude = new Etude;

        $etude->setMandat($this->get('mgate.etude_manager')->getMaxMandat());
        $etude->setNum($this->get('mgate.etude_manager')->getNouveauNumero());

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user instanceof \mgate\UserBundle\Entity\User)
            $etude->setSuiveur($user->getPersonne());

        $form = $this->createForm(new EtudeType(), $etude);
        $em = $this->getDoctrine()->getManager();

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                if (!$etude->isKnownProspect()) {
                    $etude->setProspect($etude->getNewProspect());
                }

                $em->persist($etude);
                $em->flush();

                if ($this->get('request')->get('ap')) {
                    return $this->redirect($this->generateUrl('mgateSuivi_ap_rediger', array('id' => $etude->getId())));
                } else {
                    return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $etude->getNumero())));
                }
            }
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
                    'form' => $form->createView(),
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($numero) {
        $em = $this->getDoctrine()->getManager();
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->findByNumero($numero);

        if (!$etude)
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $chartManager = $this->get('mgate.chart_manager');
        $ob = $chartManager->getGantt($etude, "suivi");


        //$deleteForm = $this->createDeleteForm($id);
        $formSuivi = $this->createForm(new SuiviEtudeType, $etude);
        return $this->render('mgateSuiviBundle:Etude:voir.html.twig', array(
                    'etude' => $etude,
                    'formSuivi' => $formSuivi->createView(),
                    'chart' => $ob,
                /* 'delete_form' => $deleteForm->createView(),  */                ));
    }
	

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($numero) {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->findByNumero($numero))
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
			
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $form = $this->createForm(new EtudeType, $etude);

        $deleteForm = $this->createDeleteForm($numero);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->persist($etude);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $etude->getNumero())));
            }
        }

        return $this->render('mgateSuiviBundle:Etude:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'etude' => $etude,
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($numero) {
        $form = $this->createDeleteForm($numero);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->findByNumero($numero))
                throw $this->createNotFoundException('L\'étude n\'existe pas !');
				
			if($this->get('mgate.etude_manager')->confidentielRefus($entity, $this->container->get('security.context')))
				throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

            /**
             * @todo Cascade remove
             */
            foreach ($entity->getPhases() as $phase) {
                $em->remove($phase); //suppression des phases 
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_homepage'));
    }

    private function createDeleteForm($numero) {
        $em = $this->getDoctrine()->getManager();
         if (!$entity = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->findByNumero($numero))
                throw $this->createNotFoundException('L\'étude n\'existe pas !');
         else
             $id = $entity->getId();
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function suiviAction() {
        $em = $this->getDoctrine()->getManager();

        $MANDAT_MAX = 10;

        $etudesParMandat = array();

        for ($i = 1; $i < $MANDAT_MAX; $i++)
            array_push($etudesParMandat, $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('mandat' => $i), array('num' => 'DESC')));

        //WARN
        /* Création d'un form personalisé sans classes (Symfony Forms without Classes)
         * 
         * Le problème qui se pose est de savoir si les données reçues sont bien destinées aux bonnes études
         * Si quelqu'un ajoute une étude ou supprime une étude pendant la soumission de se formulaire, c'est la cata
         * tout se décale de 1 étude !!
         * J'ai corrigé ce bug en cas d'ajout d'une étude. Les changements sont bien sauvegardés !!
         * Mais cette page doit être rechargée et elle l'est automatiquement. (Si js est activé !)
         * bref rien de bien fracassant. Solution qui se doit d'être temporaire bien que fonctionnelle !
         * Cependant en cas de suppression d'une étude, chose qui n'arrive pas tous les jours, les données seront perdues !!
         * Perdues Perdues !!!
         */
        $etudesEnCours = array();

        $NbrEtudes = 0;
        foreach ($etudesParMandat as $etudesInMandat)
            $NbrEtudes += count($etudesInMandat);

        $form = $this->createFormBuilder();

        $id = 0;
        foreach (array_reverse($etudesParMandat) as $etudesInMandat) {
            foreach ($etudesInMandat as $etude) {
                $form = $form->add((string) (2 * $id), 'hidden', array('label' => 'refEtude', 'data' => $etude->getReference()))
                        ->add((string) (2 * $id + 1), 'textarea', array('label' => $etude->getReference(), 'required' => false, 'data' => $etude->getStateDescription()));
                $id++;
                if ($etude->getStateID() == STATE_ID_EN_COURS)
                    array_push($etudesEnCours, $etude);
            }
        }
        $form = $form->getForm();

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $data = $form->getData();

            $id = 0;
            foreach (array_reverse($etudesParMandat) as $etudesInMandat) {
                foreach ($etudesInMandat as $etude) {
                    if ($data[2 * $id] == $etude->getReference()) {
                        if ($data[2 * $id] != $etude->getStateDescription()) {
                            $etude->setStateDescription($data[2 * $id + 1]);
                            $em->persist($etude);
                            $id++;
                        }
                    } else {
                        echo '<script>location.reload();</script>';
                    }
                }
            }
            $em->flush();
        }


        $chartManager = $this->get('mgate.chart_manager');
        $ob = $chartManager->getGanttSuivi($etudesEnCours);



        return $this->render('mgateSuiviBundle:Etude:suiviEtudes.html.twig', array(
                    'etudesParMandat' => $etudesParMandat,
                    'form' => $form->createView(),
                    'chart' => $ob,
                ));
    }
    
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function suiviQualiteAction() {
        $em = $this->getDoctrine()->getManager();

        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_COURS), array('mandat' => 'DESC', 'num' => 'DESC'));
        $etudesTerminees = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_TERMINEE), array('mandat' => 'DESC', 'num' => 'DESC'));
        $etudes = array_merge($etudesEnCours, $etudesTerminees);

        
        
        
        
        $chartManager = $this->get('mgate.chart_manager');
        $ob = $chartManager->getGanttSuivi($etudes);

        return $this->render('mgateSuiviBundle:Etude:suiviQualite.html.twig', array(
                    'etudesEnCours' => $etudesEnCours,
                    'etudesTerminees' => $etudesTerminees,
                    'chart' => $ob,
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function suiviUpdateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); 

        if (!$etude)
            throw $this->createNotFoundException('Unable to find Etude entity.');
			
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $formSuivi = $this->createForm(new SuiviEtudeType, $etude);
        if ($this->get('request')->getMethod() == 'POST') {
            $formSuivi->bind($this->get('request'));

            if ($formSuivi->isValid()) {
                $em->persist($etude);
                $em->flush();

                $return = array("responseCode" => 100, "msg" => "ok");
            }
            else
                $return = array("responseCode" => 200, "msg" => "Erreur:" . $formSuivi->getErrorsAsString());
        }


        $return = json_encode($return); //jscon encode the array
        return new Response($return, 200, array('Content-Type' => 'application/json')); //make sure it has the correct content type
    }

    private function searchArrayID(array $etudes, Etude $etude) {
        $i = 0;
        foreach ($etudes as $e) {
            if ($e->getId() == $etude->getId())
                return $i;
            else
                $i++;
        }
        return -1;
    }

    private function constructArrayAssoc(array $etudes) {
        $etudesAssoc = array();
        foreach ($etudes as $e) {
            $etudesAssoc[$e->getId()] = $this->get('mgate.etude_manager')->getRefEtude($e) . " - " . $e->getNom();
        }
        return $etudesAssoc;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function vuCAAction($id) {

        $em = $this->getDoctrine()->getManager();

        if ($id > 0)
            $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id);
        else
            $etude = $em->getRepository('mgateSuiviBundle:Etude')->findOneBy(array('stateID' => STATE_ID_EN_COURS));

        if (!$etude)
            throw $this->createNotFoundException('Unable to find Etude entity.');

        //Etudes En Négociation : stateID = 1
        $etudesEnNegociation = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_NEGOCIATION), array('mandat' => 'ASC', 'num' => 'ASC'));

        //Etudes En Cours : stateID = 2
        $etudesEnCours = $em->getRepository('mgateSuiviBundle:Etude')->findBy(array('stateID' => STATE_ID_EN_COURS), array('mandat' => 'ASC', 'num' => 'ASC'));

        $etudes = array_merge($etudesEnNegociation, $etudesEnCours);

        $id = $this->searchArrayID($etudes, $etude);

        if ($id == -1)
            throw $this->createNotFoundException('Etude incorrecte');


        $nId = $id + 1;
        $pId = $id - 1;
        if ($nId >= count($etudes))
            $nId--;
        if ($pId < 0)
            $pId = 0;

        $nextID = $etudes[$nId]->getId();
        $prevID = $etudes[$pId]->getId();

        $chartManager = $this->get('mgate.chart_manager');
        $ob = $chartManager->getGantt($etude, "suivi");


        return $this->render('mgateSuiviBundle:Etude:vuCA.html.twig', array(
                    'etude' => $etude,
                    'chart' => $ob,
                    'nextID' => $nextID,
                    'prevID' => $prevID,
                    'listEtudesNegociate' => $this->constructArrayAssoc($etudesEnNegociation),
                    'listEtudesCurrent' => $this->constructArrayAssoc($etudesEnCours),
                ));
    }

}
