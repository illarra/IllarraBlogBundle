<?php

namespace Illarra\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class Post extends EntityRepository
{
    protected $environment       = null;
    protected $entities_count    = null;
    protected $entities_per_page = 0;
    protected $pages             = 1;
    protected $label_filter      = null;
    
    public function countPages()
    {
        return ceil($this->getEntitiesCount() / $this->getEntitiesPerPage());
    }
    
    public function countEntities()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('count(p.id)')
            ->from($this->getEntityName(), 'p')
        ;
        
        $qb = $this->filterQueryByLabel($qb);
        $qb = $this->updateQueryForEnvironment($qb);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        
        return $this;
    }
    
    public function getEnvironment()
    {
        return $this->environment;
    }
    
    public function getEntitiesCount()
    {
        if (is_null($this->entities_count)) {
            $this->entities_count = $this->countEntities();
        }
        
        return $this->entities_count;
    }
    
    public function setEntitiesPerPage($entitiesPerPage)
    {
        $this->entities_per_page = $entitiesPerPage;
        
        return $this;
    }
    
    public function getEntitiesPerPage()
    {
        return $this->entities_per_page;
    }
    
    public function getPages()
    {
        if (0 == $this->getEntitiesPerPage()) {
            $this->pages = 1;
        } else {
            $this->pages = ($this->countPages() > 0) ? $this->countPages() : 1;
        }
        
        return $this->pages;
    }
    
    public function getMaxResults()
    {
        return (0 == $this->getEntitiesPerPage()) ? null : $this->getEntitiesPerPage();
    }
    
    public function getOffset($page)
    {
        return ($page == 1)
            ? null
            : ($page - 1) * $this->getEntitiesPerPage();
    }
    
    public function setLabelFilter(array $labels)
    {
        $this->label_filter = $labels;
        
        return $this;
    }
    
    public function getLabelFilter()
    {
        return $this->label_filter;
    }
    
    public function findOne($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('p, pl')
            ->from($this->getEntityName(), 'p')
            ->innerJoin('p.labels', 'pl')
            ->where('p.id = :id')->setParameter('id', $id)
        ;
        
        $qb = $this->updateQueryForEnvironment($qb);
        
        try {
            $entity = $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }
        
        return $entity;
    }
    
    public function findAllOrdered($page = 1)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityName(), 'p')
            ->orderBy('p.publishedAt', 'DESC')
        ;
        
        $qb = $this->filterQueryByLabel($qb);
        $qb = $this->updateQueryForEnvironment($qb);
        
        return $qb->getQuery()
            ->setFirstResult($this->getOffset($page))
            ->setMaxResults($this->getMaxResults())
            ->getResult()
        ;
    }
    
    private function filterQueryByLabel($qb)
    {
        if (!is_null($this->getLabelFilter())) {
            $qb
                ->innerJoin('p.labels', 'pl')
                ->andWhere($qb->expr()->in('pl.id', $this->getLabelFilter()))
            ;
        }
        
        return $qb;
    }
    
    private function updateQueryForEnvironment($qb)
    {
        if ('prod' == $this->getEnvironment()) {
            $qb
                ->andWhere('p.isVisible = :isVisible')->setParameter('isVisible', true)
                ->andWhere('p.publishedAt <= :now')->setParameter('now', date('Y-m-d H:i', time()))
            ;
        }
        
        return $qb;
    }
}
