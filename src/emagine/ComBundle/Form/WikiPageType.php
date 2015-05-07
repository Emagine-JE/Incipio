<?php

namespace emagine\ComBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WikipageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('slug')
            ->add('category', 'genemu_jqueryselect2_entity', array(
                'class' => 'emagine\ComBundle\Entity\WikiPageCategory',
                'property' => 'titre',
                'required' => true,
                'label' => 'Categorie',
                ))
            ->add('contenu')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'emagine\ComBundle\Entity\Wikipage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'emagine_combundle_wikipage';
    }
}
