<?php

namespace Illarra\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class PostTranslation
{
    use \Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
    
    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $excerpt;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $text;
    
    /**
     * @param string $title
     * @return PostTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @param string $excerpt
     * @return PostTranslation
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
        
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }
    
    /**
     * @param string $text
     * @return PostTranslation
     */
    public function setText($text)
    {
        $this->text = $text;
        
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * @return array
     */
    public function getSluggableFields()
    {
        return ['title'];
    }
}