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

namespace mgate\TresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

use mgate\TresoBundle\Form\NoteDeFraisDetailType;
use mgate\PersonneBundle\Entity\PersonneRepository;

class NoteDeFraisType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder->add('mandat', 'integer', array('label'=>'Mandat', 'required' => true))
                ->add('numero', 'integer', array('label'=>'Numéro de la Note de Frais', 'required' => true))
                ->add('objet', 'textarea', 
                    array('label' => 'Objet de la Note de Frais',
                        'required' => false, 
                        'attr'=>array(
                            'cols'=>'100%', 
                            'rows'=>5)
                        )
                    )
                ->add('details', 'collection', array(
                    'type' => new NoteDeFraisDetailType,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                ))
                ->add('demandeur','genemu_jqueryselect2_entity',array (
                      'label' => 'Demandeur',
                       'class' => 'mgate\\PersonneBundle\\Entity\\Personne',
                       'property' => 'prenomNom',
                       'query_builder' => function(PersonneRepository $pr) {
                            return $pr->getMembreOnly();
                        },
                       'required' => true))
                ->add('date', 'genemu_jquerydate', array('label'=>'Date', 'required'=>true, 'widget'=>'single_text'));
    }

    public function getName() {
        return 'mgate_tresobundle_notedefraistype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'mgate\TresoBundle\Entity\NoteDeFrais',
        ));
    }
    


}