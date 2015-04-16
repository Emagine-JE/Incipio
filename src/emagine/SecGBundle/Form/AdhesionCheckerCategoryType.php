<?php

namespace emagine\SecGBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilder;

class AdhesionCheckerCategoryType extends AbstractType {

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nom', 'text', array('required' => true))
                ->add('description', 'textarea');
    }

    public function getName() {
        return 'emagine_secGBundle_adhesionCheckerCategoryType';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'emagine\SecGBundle\Entity\AdhesionCheckerCategory',
        ));
    }

}

