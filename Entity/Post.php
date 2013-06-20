<?php

namespace Illarra\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Illarra\BlogBundle\Interfaces;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Post implements Interfaces\PostInterface
{
    use \Knp\DoctrineBehaviors\Model\Timestampable\Timestampable,
        \Illarra\CoreBundle\Traits\Entity\Featured,
        \Illarra\CoreBundle\Traits\Entity\Visible;
    
    public function __call($method, $arguments)
    {
        $method = in_array($method, ['slug', 'title', 'excerpt', 'text'])
            ? 'get'.$method
            : $method;
        
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $publishedAt;
    
    /**
     * @ORM\ManyToMany(targetEntity="Label", inversedBy="posts")
     * @ORM\JoinTable(name="post_label_rel")
     */
    protected $labels;
    
    public function __construct()
    {
        $this->publishedAt = new \DateTime('now');
        $this->isFeatured = false;
        $this->isVisible = true;
        $this->labels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }
    
    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param \DateTime $publishedAt
     * @return Post
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
    
    /**
     * @param Label $label
     * @return Post
     */
    public function addLabel(Interfaces\LabelInterface $label)
    {
        $label->addPost($this);
        $this->labels[] = $label;
        
        return $this;
    }
    
    /**
     * @param Label $label
     */
    public function removeLabel(Interfaces\LabelInterface $label)
    {
        $this->labels->removeElement($label);
    }
    
    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLabels()
    {
        return $this->labels;
    }
    
    //
    // URL helpers
    //
    
    /**
     * @return string
     */
    public function getUrlRoute()
    {
        return 'post_view';
    }
    
    /**
     * @param integer $page
     * @return array
     */
    public function getUrlParams($locale = false)
    {        
        if ($locale) {
            $translation = $this->findTranslationByLocale($locale);
            
            return [
                'id'      => $this->getId(),
                'slug'    => $translation->getSlug(),
                '_locale' => $locale
            ];
        }
        
        return [
            'id'   => $this->getId(),
            'slug' => $this->getSlug()
        ];
    }
}