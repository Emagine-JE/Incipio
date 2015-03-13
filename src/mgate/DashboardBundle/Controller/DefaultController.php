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


namespace mgate\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\PersonneBundle\Entity\Personne;

class DefaultController extends Controller
{
    public function indexAction()
    {   

        // On récupère le service
        $security = $this->get('security.context');

        // On récupère le token
        $token = $security->getToken();

        // on récupère l'utilisateur
        $user = $token->getUser();

        $em = $this->getDoctrine()->getManager();

        $personne = $em->getRepository('mgatePersonneBundle:Personne')->findByUser($user->getId());

        //page pour les membres
        if (($security->isGranted('ROLE_MEMBRE') && !empty($personne)) 
            || $security->isGranted('ROLE_ADMIN')) {
            return $this->render('mgateDashboardBundle:Default:index.html.twig');
        }

        //page pour les utilisateur qui n'ont pas complété l'inscription
        return $this->render('mgateDashboardBundle:Default:indexUser.html.twig',array(
            'hasPersonne'       => !empty($personne),
            'hasInscription'    =>  false,
        ));
        
    }


    public function navbarAction()
    {
        $em = $this->getDoctrine()->getManager();
          
        $user = $this->container->get('security.context')->getToken()->getUser()->getPersonne();
        
        //Etudes Suiveur
        $etudesSuiveur = array();
        foreach($em->getRepository('mgateSuiviBundle:Etude')->findBy(array('suiveur' => $user), array('mandat'=> 'DESC', 'id'=> 'DESC')) as $etude)
        {
            $stateID = $etude->getStateID();
            if( $stateID <= 2 )
             array_push($etudesSuiveur, $etude);
        }
        
        return $this->render('mgateDashboardBundle:Navbar:layout.html.twig', array('etudesSuiveur' => $etudesSuiveur));
    }
}
