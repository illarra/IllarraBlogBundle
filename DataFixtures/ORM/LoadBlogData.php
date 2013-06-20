<?php

namespace Illarra\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illarra\BlogBundle\Entity\Label;
use Illarra\BlogBundle\Entity\Post;

class LoadBlogData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Labels
        $responsive = new Label();
        $responsive->translate('es')->setTitle('label');
        $manager->persist($responsive);
        
        // Projects
        $post1 = new Post();
        $post1->translate('es')->setTitle('post_es_title');
        $post1->translate('es')->setExcerpt('post_es_excerpt');
        $post1->translate('es')->setText('post_es_text');
        $post1
            ->setPublishedAt(new \DateTime('now'))
            ->addLabel($responsive)
        ;
        $manager->persist($post1);
        
        $manager->flush();
    }
}
