<?php

namespace Illarra\BlogBundle\Controller\Seo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Illarra\CoreBundle\Helper\Sitemap\Sitemap;
use Illarra\CoreBundle\Helper\Sitemap\Url;

class PostController extends Controller
{
    private function getPostRepository($labelFilter = null)
    {
        $repository = $this->getDoctrine()->getRepository($this->container->getParameter('illarra_blog.post_class'));
        $repository
            ->setEnvironment('prod')
            ->setEntitiesPerPage(100)
        ;
        
        return $repository;
    }
    
    /**
     * @Route("/sitemap-blog-post-index.xml", name="sitemap_illarra_blog_post_index")
     */
    public function indexAction()
    {
        $sitemap = new Sitemap();
        
        for ($page = 1, $pages = $this->getPostRepository()->getPages(); $page == $pages; $page++) {
            $sitemap->addUrl(new Url($this->generateUrl('sitemap_illarra_blog_post_list', ['page' => $page], true)));
        }
        
        $response = new Response($sitemap->renderIndex());
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }
    
    /**
     * @Route("/sitemap-blog-post-{page}.xml", name="sitemap_illarra_blog_post_list", requirements={"page" = "\d+"})
     */
    public function listAction($page)
    {
        $sitemap = new Sitemap();
        
        foreach ($this->getPostRepository()->findAllOrdered($page) as $post) {
            foreach ($post->getTranslations() as $translation) {
                $sitemap->addUrl(new Url($this->generateUrl($post->getUrlRoute(), $post->getUrlParams($translation->getLocale()), true)));
            }
        }
        
        $response = new Response($sitemap->render());
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }
}
