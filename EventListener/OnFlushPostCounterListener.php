<?php

namespace Illarra\BlogBundle\EventListener;

class OnFlushPostCounterListener
{
    use \Illarra\CoreBundle\Traits\EventListener\Doctrine\OnFlushCounterListener;
    
    public function getOwnerEntityClass()
    {
        return 'Post';
    }
    
    public function getManyToOneTargetEntitiesClassAndField()
    {
        return [];
    }
    
    public function getManyToManyTargetEntitiesClassAndField()
    {
        return [
            'Label' => 'labels'
        ];
    }
    
    public function getCounterFieldInTargetEntities()
    {
        return 'posts';
    }

}
