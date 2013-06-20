<?php

namespace Illarra\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LabelTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'hidden')
            ->add('name', null, ['label' => 'label.label.name'])
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Illarra\BlogBundle\Entity\LabelTranslation'
        ));
    }
    
    public function getName()
    {
        return 'illarra_blogbundle_labeltranslationtype';
    }
}
