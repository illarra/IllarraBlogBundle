<?php

namespace Illarra\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'hidden')
            ->add('title', null, ['label' => 'post.label.title'])
            ->add('excerpt', null, ['label' => 'post.label.excerpt'])
            ->add('text', null, ['label' => 'post.label.text', 'attr' => ['class' => 'js-markdown']])
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Illarra\BlogBundle\Entity\PostTranslation'
        ));
    }
    
    public function getName()
    {
        return 'illarra_blogbundle_posttranslationtype';
    }
}
