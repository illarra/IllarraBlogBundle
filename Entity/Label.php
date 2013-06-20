<?php

namespace Illarra\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Illarra\BlogBundle\Interfaces;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Label implements Interfaces\LabelInterface
{
    use \Knp\DoctrineBehaviors\Model\Timestampable\Timestampable,
        \Illarra\CoreBundle\Traits\Entity\Counter;
    
    public function __call($method, $arguments)
    {
        $method = in_array($method, ['slug', 'name'])
            ? 'get'.$method
            : $method;
        
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
    
    public function __toString()
    {
        return $this->getSlug();
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="labels")
     */
    protected $posts;
    
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param Post $post
     * @return Label
     */
    public function addPost(Interfaces\PostInterface $post)
    {
        $this->posts[] = $post;
        
        return $this;
    }
    
    /**
     * @param Post $post
     */
    public function removePost(Interfaces\PostInterface $post)
    {
        $this->posts->removeElement($post);
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
    
    /**
     * @return array
     */
    public function getCounterFields()
    {
        return ['posts'];
    }
    
    //
    // URL helpers
    //
    
    /**
     * @return string
     */
    public function getUrlRoute()
    {
        return 'label_view';
    }
    
    /**
     * @param integer $page
     * @return array
     */
    public function getUrlParams($page = 1)
    {
        return [
            'id'   => $this->getId(),
            'slug' => $this->getSlug(),
            'page' => $page
        ];
    }
}