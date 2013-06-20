<?php

namespace Illarra\BlogBundle\EventListener;

use Illarra\CoreBundle\Event\ConfigureSitemapEvent;

class ConfigureSitemapListener
{
    /**
     * @param \Illarra\CoreBundle\Event\ConfigureSitemapEvent $event
     */
    public function onMenuConfigure(ConfigureSitemapEvent $event)
    {
        $menu = $event->getMenu();
        
        $menu->addChild('illarra_blog_post', array('uri' => 'sitemap_illarra_blog_post_index')); // Don't use 'path'
    }
}
