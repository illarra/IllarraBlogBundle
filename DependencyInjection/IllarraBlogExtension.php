<?php

namespace Illarra\BlogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class IllarraBlogExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        // Set parameters
        $container->setParameter('illarra_blog.type', $config['type']);
        $container->setParameter('illarra_blog.post_class', $config['post_class']);
        $container->setParameter('illarra_blog.label_class', $config['label_class']);
        $container->setParameter('illarra_blog.posts_per_page', $config['posts_per_page']);
        $container->setParameter('illarra_blog.comments.disqus_shortname', $config['comments']['disqus_shortname']);
        $container->setParameter('illarra_blog.feed.number_of_entries', $config['feed']['number_of_entries']);
    }
}
