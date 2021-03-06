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


namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Mandat;
use mgate\PersonneBundle\Form\MembreType;

use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Entity\RelatedDocument;

class MembreController extends Controller {

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();
        $categories = $em->getRepository('emagineSecGBundle:AdhesionCheckerCategory')
                        ->createQueryBuilder('l') 
                        ->select('COUNT(l)') 
                        ->getQuery() 
                        ->getSingleScalarResult();
        
        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
                    'membres'    => $entities,
                    'categories' => $categories,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listIntervenantsAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();
        
        $intervenants = array();
        
        
        foreach($entities as $etude){
            foreach($etude->getMissions() as $mission){
                $intervenant = $mission->getIntervenant();
                if($intervenant != NULL)
                    $intervenants[$intervenant->getPersonne()->getPrenomNom()] = $intervenant;          
            }
        }

        return $this->render('mgatePersonneBundle:Membre:indexIntervenants.html.twig', array(
                    'membres' => $intervenants,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function statistiqueAction($page) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();
        
            
        $membresActifs = array();
        foreach($entities as $membre){
            foreach ($membre->getMandats() as $mandat){
                if($mandat->getPoste()->getIntitule() == 'Membre' && $mandat->getDebutMandat() < new \DateTime("now") && $mandat->getFinMandat() > new \DateTime("now"))
                    $membresActifs[] = $membre;
            }                
        }
        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
                    'membres' => $membresActifs,
                ));
    }

    /**
     * @Secure(roles="ROLE_MEMBRE")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Membre')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Le membre demandé n\'existe pas !');
        }
        
        return $this->render('mgatePersonneBundle:Membre:voir.html.twig', array(
                    'membre' => $entity,
        ));
    }

    /**
     * Modification ou création d'un membre
     * deux niveaux d'utiliations :
     * USER -> rattacher un profile membre à son compte ou modifier son profile
     * CA   -> créer un membre
     */
    public function modifierAction($id) {

        //Paramètres de contrôles
        $security = $this->get('security.context');
        $em = $this->getDoctrine()->getManager();
        $documentManager = $this->get('mgate.documentManager');
        $hasROLE_CA = $security->isGranted('ROLE_CA');

        // On récupère le token
        $token = $security->getToken();
        // on récupère l'utilisateur
        $user = $token->getUser();

        //securité utilisateur non CA
        if(!$hasROLE_CA){
            $personne = $em->getRepository('mgatePersonneBundle:Personne')->findOneByUser($user->getId());

            //c'est une édition ou une creation (0 => creation)
            if($id == 0){
                //l'utilisateur ne peux pas créer un nouveau profile si il en en déjà un
                if(!empty($personne)){
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }else{
                $membre = $personne->getMembre();
                //L'utilisateur n'édit pas sont propre profile
                if($membre->getId() != $id){
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }
        }
 
        //create
        if (!$membre = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id)) {
            $membre = new Membre;
            
            $now = new \DateTime("now");
            $now->modify('+ 3 year');            
            
            $membre->setPromotion($now->format("Y"));
            
            $now = new \DateTime("now");
            $now->modify('- 20 year');
            $membre->setDateDeNaissance($now);
        }
        
        // Mail étudiant
        if(!$membre->getEmailEMSE()){
            $membre->setEmailEMSE($this->getEmailEtu($membre));
        }

        //creation du formulaire
        $membreType = new MembreType();
        $membreType->setAdvType($hasROLE_CA);
        $form = $this->createForm($membreType , $membre);           

        //suppression membre
        $deleteForm = $this->createDeleteForm($id);
        //suppression cascade manda (AC)
        $mandatsToRemove = $membre->getMandats()->toArray();

        //reponse au formulaire POST
        if ($this->get('request')->getMethod() == 'POST') {

            $form->bind($this->get('request'));
            $photoUpload = $form->get('photo')->getData();
            
            //validation
            if ($form->isValid()) {
                
                // TODO TOREMOVE Specifique EMSE
                if($membre->getPersonne()){
                    // Photo de l'étudiant 
                    $path =  $membre->getPromotion() .'/' . 
                        preg_replace(
                            '#[^a-zA-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜáàâäéèêëíìîïóòôöúùûüÇç\-_]#',
                            '_',
                        mb_strtolower($membre->getPersonne()->getNom(),'UTF-8')
                    ) . '_'.
                        preg_replace(
                            '#[^a-zA-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜáàâäéèêëíìîïóòôöúùûüÇç\-_]#',
                            '_',
                        mb_strtolower($membre->getPersonne()->getPrenom(), 'UTF-8')
                    );
                }else{
                    $path = '';
                }

                $promo = $membre->getPromotion();             
                /**
                 * Traitement de l'image de profil
                 */
                if($membre->getPersonne()){
                    $authorizedMIMEType = array('image/jpeg', 'image/png', 'image/bmp');
                    $photoInformation = new RelatedDocument();                    
                    $photoInformation->setMembre($membre);
                    $name = 'Photo - '.$membre->getIdentifiant(). ' - '.$membre->getPersonne()->getPrenomNom();
                    
                    if($photoUpload){
                        $document = $documentManager->uploadDocumentFromFile($photoUpload, $authorizedMIMEType, $name, $photoInformation, true);
                        $membre->setPhotoURI($document->getWebPath());
                    }elseif(!$membre->getPhotoURI() && $promo != null && $membre->getPersonne()){ // Spécifique EMSE
                        //$ressourceURL = 'http://ismin.emse.fr/ismin/Photos/P'.urlencode($path);
                        //$headers = get_headers($ressourceURL);
                        //if(preg_match('#200#', $headers[0])){
                            //$document = $documentManager->uploadDocumentFromUrl($ressourceURL, $authorizedMIMEType, $name, $photoInformation, true);
                            //$membre->setPhotoURI($document->getWebPath());
                        //}
                    }                    
                }                

                /**
                 * Traitement des postes si l'utilisateur a le niveau de secu suffisant
                 */
                if ($hasROLE_CA && $this->get('request')->get('add')) {
                    $mandatNew = new Mandat;
                    $poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->findOneBy(array("intitule" => "Membre"));
                    $dt = new \DateTime("now");
                    $dtl = clone $dt;
                    $dtl->modify('+1 year');

                    if ($poste)
                        $mandatNew->setPoste($poste);
                    $mandatNew->setMembre($membre);
                    $mandatNew->setDebutMandat($dt);
                    $mandatNew->setFinMandat($dtl);
                    $membre->addMandat($mandatNew);
                }

                /**
                 * Création des Identifiants
                 */
                if (!$membre->getIdentifiant()) {
                    $initial = substr($membre->getPersonne()->getPrenom(), 0, 1) . substr($membre->getPersonne()->getNom(), 0, 1);
                    $ident = count($em->getRepository('mgate\PersonneBundle\Entity\Membre')->findBy(array("identifiant" => $initial))) + 1;
                    while ($em->getRepository('mgate\PersonneBundle\Entity\Membre')->findOneBy(array("identifiant" => $initial . $ident)))
                        $ident++;
                    $membre->setIdentifiant(strtoupper($initial . $ident));
                }
                
                /**
                 * Si c'est un nouveau membre et qu'on ajoute un poste
                 */
                if(isset($now)){
                    $em->persist($membre);
                    $em->flush();

                    //un utilisateur créer son profile
                    if($id == 0 && !$hasROLE_CA){
                        $userManager = $this->get('fos_user.user_manager');
                        $membre = $em->getRepository('mgatePersonneBundle:Membre')->findOneByIdentifiant($membre->getIdentifiant());
                        $user->setPersonne($membre->getPersonne());
                        $userManager->updateUser($user);
                    } 

                    return $this->redirect($this->generateUrl('mgatePersonne_membre_modifier', array('id' => $membre->getId())));                    
                }
                
                // Suppression des mandat à supprimer
                    //Recherche des mandats supprimés
                foreach ($membre->getMandats() as $mandat){
                    $key = array_search($mandat, $mandatsToRemove);
                    if($key !== FALSE)
                        array_splice($mandatsToRemove, $key, 1);
                }
                    //Supression de la BDD
                foreach ($mandatsToRemove as $mandat){
                    $em->remove($mandat);
                }

                $em->persist($membre); // persist $etude / $form->getData()
                $em->flush();  
            }else{
                //formulaire invalise 
                //TODO message Flash
                var_dump($form->getErrorsAsString(1));die();
            }
        }


        // TODO A modifier, l'ajout de poste dois se faire en js cf formation membre
        //if ($this->get('request')->get('save'))
         //   return $this->redirect($this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre->getId())));
  
        //creation du formulaire pour ajouter de nouveau poste (astuce étrange) 
        $membreType = new MembreType();
        $membreType->setAdvType($hasROLE_CA);
        $form = $this->createForm($membreType , $membre);   

        return $this->render('mgatePersonneBundle:Membre:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'photoURI' => $membre->getPhotoURI(),
                ));            
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function deleteAction($id) {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id))
                throw $this->createNotFoundException('Le membre demandé n\'existe pas !');

            if ($entity->getPersonne()) {
                $entity->getPersonne()->setMembre(null);
                if ($entity->getPersonne()->getUser())// pour pouvoir réattribuer le compte
                    $entity->getPersonne()->getUser()->setPersonne(null);
                $entity->getPersonne()->setUser(null);
            }
            $entity->setPersonne(null);
            //est-ce qu'on supprime la personne aussi ?

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgatePersonne_membre_homepage'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }
    
    private function enMinusculeSansAccent($texte){
        $texte = mb_strtolower($texte, 'UTF-8');
        $texte = str_replace(
            array(
                'à', 'â', 'ä', 'á', 'ã', 'å',
                'î', 'ï', 'ì', 'í', 
                'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
                'ù', 'û', 'ü', 'ú', 
                'é', 'è', 'ê', 'ë', 
                'ç', 'ÿ', 'ñ', 
            ),
            array(
                'a', 'a', 'a', 'a', 'a', 'a', 
                'i', 'i', 'i', 'i', 
                'o', 'o', 'o', 'o', 'o', 'o', 
                'u', 'u', 'u', 'u', 
                'e', 'e', 'e', 'e', 
                'c', 'y', 'n', 
            ),
            $texte
        );
        return $texte;        
    }
    
    /**
     * //TODO correspond pas forcement à nos adress email (AC)
     * Get Adresse Mail Etu
     * @return string format@etu.emse.fr
     */
    public function getEmailEtu($membre) {
        $junior = $this->container->getParameter('junior');
        $emailEtu = array_key_exists('domaineEmailEtu', $junior) ? '@'.$junior['domaineEmailEtu'] : '@';
        $emailAncien = array_key_exists('domaineEmailAncien', $junior) ? '@'.$junior['domaineEmailAncien'] : '@';        
        
        $now = new \DateTime("now");
        $now = (int) $now->format("Y");

        if ($promo = $membre->getPromotion() && $membre->getPersonne()) {
            if ($promo < $now)
                return preg_replace('#[^a-zA-Z.0-9_]#','',$this->enMinusculeSansAccent($membre->getPersonne()->getPrenom(). '.' . $membre->getPersonne()->getNom())) . $emailAncien;
            if ($promo < 2014) // Spécificité emse
                //TODO TOREMOVE TOFIX
                return preg_replace('#[^a-zA-Z.0-9_]#','',$this->enMinusculeSansAccent(substr($membre->getPersonne ()->getPrenom (), 0, 1) . $membre->getPersonne ()->getNom ())) . $emailEtu;
            else
            return preg_replace('#[^a-zA-Z.0-9_]#','',$this->enMinusculeSansAccent($membre->getPersonne()->getPrenom() . '.' . $membre->getPersonne()->getNom())) . $emailEtu;
        }
        else if($membre->getPersonne()){
            return preg_replace('#[^a-zA-Z.0-9_]#','',$this->enMinusculeSansAccent($membre->getPersonne()->getPrenom() . '.' . $membre->getPersonne()->getNom())) . $emailEtu;
        } else return null;
    }

}
