<?php

namespace OSM\ComCollectBundle\Utils;

use Doctrine\ORM\EntityManager;
use OSM\ComCollectBundle\Entity\Wikipage;

class WikipagePersistence {     
           
    /**
     * doctrine EM instance
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager; 
           
    /**
     * initializes the EntityManager
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        
        $this->entityManager = $entityManager;
    }
    
    /**
     * saves the given PageData to the persistence layer (DB)
     * 
     * @param array $pages
     * @return array
     */
    public function save($pages) {
        
        foreach ($pages as $page) {
            
            $this->removeOldData($page);
            $this->entityManager->persist($page);
        }
        
        $this->setLastCollect();
        
        $this->entityManager->flush();
    }
    
    /**
     * removes existing data by given Wikipage
     * 
     * @param Wikipage $page
     */
    private function removeOldData(Wikipage $page) {
        
        //get existing page
        $oldPage = $this->entityManager->getRepository('OSMComCollectBundle:Wikipage')->findOneByName($page->getName());            
        
        if ($oldPage) {

            $this->entityManager->remove($oldPage);
            $this->entityManager->flush();
        }
    }
    
    /**
     * sets the current time for LastCollect
     */
    private function setLastCollect() {
       
        $repository = $this->entityManager->getRepository('OSMComCollectBundle:LastCollect');
        
        $lastCollect = $repository->findOneBy(['channel' => 'wikipage']);
        $lastCollect->setTime(new \DateTime('NOW'));
        
        $this->entityManager->persist($lastCollect);
    }
    
}
