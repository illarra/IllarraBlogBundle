<?php

namespace Illarra\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publishedAt', null, ['label' => 'post.label.publishedAt'])
            ->add('labels', null, ['label' => 'post.label.labels', 'required' => false])
            ->add('translations', 'collection', array(
                'cascade_validation' => true,
                'type' => new PostTranslationType(),
                'allow_add' => true,
                'allow_delete' =>  true,
                'prototype' => true,
                'by_reference' => false,
                'options' => array(
                    'data_class' => $this->container->getParameter('illarra_blog.post_class') . 'Translation',
                )
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->container->getParameter('illarra_blog.post_class')
        ));
    }
    
    public function getName()
    {
        return 'illarra_blogbundle_posttype';
    }
}
