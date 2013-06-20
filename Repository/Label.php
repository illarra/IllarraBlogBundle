<?php

namespace Illarra\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class Label extends EntityRepository
{
    use \Illarra\CoreBundle\Traits\Repository\Paginator;
    
    public function getOrderFields()
    {
        return ['slug'];
    }
}
