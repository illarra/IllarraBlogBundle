<?php

namespace Illarra\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('illarra_blog');
        
        $blogTypes = ['mysql', 'sqlite', 'mssql'];
        
        $rootNode
            ->children()
                ->arrayNode('type')
                    ->info('Blog type configuration.')
                    ->children()
                        ->booleanNode('one_blog_per_locale')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('predefined_locales')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('post_class')
                    ->defaultValue('\Illarra\BlogBundle\Entity\Post')
                    ->info('Name of the post entity class.')
                ->end()
                ->scalarNode('label_class')
                    ->defaultValue('\Illarra\BlogBundle\Entity\Label')
                    ->info('Name of the label entity class.')
                ->end()
                ->scalarNode('posts_per_page')
                    ->defaultValue(0)
                    ->info('Number of posts to display per page. "0" (default) removes pagination.')
                ->end()
            ->end()
        ;
        
        $this->addCommentsSection($rootNode);
        $this->addFeedSection($rootNode);
        
        return $treeBuilder;
    }
    
    private function addCommentsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('comments')
                    ->addDefaultsIfNotSet()
                    ->info('Comments module configuration.')
                    ->children()
                        ->scalarNode('disqus_shortname')
                            ->defaultValue(null)
                            ->info('Disqus shortname. Default is "null", and disables comments.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    private function addFeedSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('feed')
                    ->addDefaultsIfNotSet()
                    ->info('RSS feed configuration.')
                    ->children()
                        ->scalarNode('number_of_entries')
                            ->defaultValue(25)
                            ->info('Number of entries to include in the blog feed. Default is "25".')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
