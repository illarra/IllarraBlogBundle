<?php

namespace Illarra\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LabelType extends AbstractType
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'collection', array(
                'cascade_validation' => true,
                'type' => new LabelTranslationType(),
                'allow_add' => true,
                'by_reference' => false,
                'options' => array(
                    'data_class' => $this->container->getParameter('illarra_blog.label_class') . 'Translation',
                )
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->container->getParameter('illarra_blog.label_class')
        ));
    }
    
    public function getName()
    {
        return 'illarra_blogbundle_labeltype';
    }
}
