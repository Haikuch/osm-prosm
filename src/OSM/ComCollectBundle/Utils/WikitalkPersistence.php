<?php

namespace OSM\ComCollectBundle\Utils;

use Doctrine\ORM\EntityManager;

class WikitalkPersistence {     
           
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
            
            $this->replaceData($page);
        }
        
        $this->entityManager->flush();
            
        $this->setLastCollect();
    }
    
    /**
     * removes old data and persists new
     * 
     * @param type $page
     */
    private function replaceData($page) {
        
        $this->removeOldData($page['name']);
            
        foreach ($page['posts'] as $post) {

            $this->entityManager->persist($post);
        }
    }
    
    /**
     * removes existing data by given pageName
     * 
     * @param string $pageName
     */
    private function removeOldData($pageName) {
        
        //remove existing data from DB
        $oldThreads = $this->entityManager->getRepository('OSMComCollectBundle:WikitalkThread')->findByPageName($pageName);            

        foreach ($oldThreads as $oldThread) {

            $this->entityManager->remove($oldThread);
        }
        
        $this->entityManager->flush();
    }
    
    /**
     * sets the current time for LastCollect
     */
    private function setLastCollect() {
       
        $repository = $this->entityManager->getRepository('OSMComCollectBundle:LastCollect');
        
        $lastCollect = $repository->findOneBy(['channel' => 'wikitalk']);
        $lastCollect->setTime(new \DateTime('NOW'));        
        
        $this->entityManager->persist($lastCollect);
    }
    
}
