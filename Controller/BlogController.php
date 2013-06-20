<?php

namespace Illarra\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends Controller
{
    use \Illarra\CoreBundle\Traits\Controller\CoreConfiguration;
    
    private function getPostRepository($labelFilter = null)
    {
        $repository = $this->getDoctrine()->getRepository($this->container->getParameter('illarra_blog.post_class'));
        $repository
            ->setEnvironment($this->container->get('kernel')->getEnvironment())
            ->setEntitiesPerPage($this->container->getParameter('illarra_blog.posts_per_page'))
        ;
        
        if (!is_null($labelFilter)) {
            $repository->setLabelFilter($labelFilter);
        }
        
        return $repository;
    }
    
    /**
     * @Route("/{page}", name="post_index", defaults={"page" = 1}, requirements={"page" = "\d+"})
     * @Template()
     */
    public function indexAction($page)
    {
        if ($page < 1) {
            return $this->redirect($this->generateUrl('post_index'));
        }
        
        $repository = $this->getPostRepository();
        
        if ($page > $repository->getPages()) {
            return $this->redirect($this->generateUrl('post_index', array('page' => $repository->getPages())));
        }
        
        return array(
            'page'         => $page,
            'pages'        => $repository->getPages(),
            'posts'        => $repository->findAllOrdered($page),
            'postsPerPage' => $repository->getEntitiesPerPage(),
        );
    }
    
    /**
     * @Route("/{id}/{slug}", name="post_view", requirements={"id" = "\d+"})
     * @Template()
     */
    public function viewAction($id, $slug)
    {
        $repository = $this->getPostRepository();
        $post = $repository->findOneById($id);
        
        if (!$post) {
            throw $this->createNotFoundException('No post found with id '.$id);
        }
        
        if ($post->getSlug() != $slug) {
            return $this->redirect($this->generateUrl($post->getUrlRoute(), $post->getUrlParams()));
        }
        
        return array(
            'post' => $post,
        );
    }
    
    /**
     * @Route("/labels/{id}/{slug}/{page}", name="label_view", defaults={"page" = 1}, requirements={"id" = "\d+", "page" = "\d+"})
     * @Template()
     */
    public function labelAction($id, $slug, $page)
    {
        $repository = $this->getDoctrine()->getRepository('IllarraBlogBundle:Label');
        $label = $repository->findOneById($id);
        
        if (!$label) {
            throw $this->createNotFoundException('No label found with id '.$id);
        }
        
        if ($page < 1 || $label->getTitle() != $slug) { // TODO: update once we have getSlug()
            return $this->redirect($this->generateUrl($label->getUrlRoute(), $label->getUrlParams()));
        }
        
        $repository = $this->getPostRepository(array($label->getId()));
        
        if ($page > $repository->getPages()) {
            return $this->redirect($this->generateUrl($label->getUrlRoute(), array_merge(
                $label->getUrlParams(),
                array('page' => $repository->getPages())
            )));
        }
        
        return array(
            'label'        => $label,
            'page'         => $page,
            'pages'        => $repository->getPages(),
            'posts'        => $repository->findAllOrdered($page),
            'postsPerPage' => $repository->getEntitiesPerPage(),
        );
    }
    
    /**
     * @Route("/rss", name="rss_feed")
     */
    public function rssAction()
    {
        $feed = new \Zend\Feed\Writer\Feed; // http://framework.zend.com/manual/2.0/en/modules/zend.feed.writer.html
        $feed
            ->setGenerator('illarra.com')
            ->setTitle('Feed')
            ->setDescription('Description')
            ->setLink($this->generateUrl('post_index', array(), true))
            ->setFeedLink($this->generateUrl('rss_feed', array(), true), 'rss')
            ->setDateModified(time())
            ->setLanguage($this->getRequest()->getLocale())
        ;
        
        $blogHasDisqusComments = !is_null($this->container->getParameter('illarra_blog.comments.disqus_shortname'));
        
        $repository = $this->getPostRepository();
        $repository->setEntitiesPerPage($this->container->getParameter('illarra_blog.feed.number_of_entries')); 
        
        foreach ($repository->findAllOrdered() as $post) {
            $url = $this->generateUrl($post->getUrlRoute(), $post->getUrlParams(), true);
            
            $entry = $feed->createEntry();
            $entry
                ->setTitle($post->getTitle())
                ->setDescription($post->getExcerpt())
                ->setLink($url)
                ->setDateModified($post->getPublishedAt())
            ;
            
            if ($blogHasDisqusComments) {
                $entry
                    ->setCommentLink($url.'#disqus_thread')
                ;
            }
            
            $feed->addEntry($entry);
        }
        
        $response = new Response($feed->export('rss'));
        $response->headers->set('Content-Type', 'application/rss+xml');
        
        return $response;
    }
}
