<?php

namespace Illarra\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Illarra\BlogBundle\Interfaces;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class LabelTranslation
{
    use \Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
    
    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $name;
    
    /**
     * @param string $name
     * @return LabelTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return array
     */
    public function getSluggableFields()
    {
        return ['name'];
    }
}
