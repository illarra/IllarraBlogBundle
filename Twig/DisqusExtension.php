<?php

namespace Illarra\BlogBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;

class DisqusExtension extends \Twig_Extension
{
    private $disqusShortname;
    
    public function __construct($disqusShortname)
    {
        $this->disqusShortname = $disqusShortname;
    }
    
    public function getFunctions()
    {
        return array(
            'disqus' => new \Twig_Function_Method($this, 'renderDisqus', array(
                'needs_environment' => true, 
                'is_safe'           => array('html'),
            )),
        );
    }
    
    public function renderDisqus(\Twig_Environment $twig, $postId)
    {
        if (is_null($this->disqusShortname)) {
            return '';
        }
        
        return $twig->render('IllarraBlogBundle:Blog:disqus.html.twig', array(
            'post_id'          => $postId,
            'disqus_shortname' => $this->disqusShortname,
        ));
    }
    
    public function getName()
    {
        return 'disqus_extension';
    }
}
