<?php

namespace OSM\ComCollectBundle\Utils;

use Doctrine\ORM\EntityManager;

class ForumPersistence {    
    
    /**
     * doctrine EM instance
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /** 
     * newly created ForumThread Entities that 
     * are not yet saved to persistence layer
     * 
     * @var array 
     */
    private $newThreads = [];
    
    /**
     * the persisted ForumPost Entities
     * @var array 
     */
    private $persistedPosts = [];
    
    /**
     * initializes the EntityManager
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        
        $this->entityManager = $entityManager;
    }
    
    /**
     * save the given PostData to the persistence layer (DB)
     * returns the successfull persisted ForumPost Entities
     * 
     * @param array $datas
     * @return array
     */
    public function save($datas) {
        
        foreach ($datas as $data) {
            
            $this->persistPost($data);
        }
        
        $this->entityManager->flush();
        
        $this->setLastCollect();
        
        return $this->persistedPosts;
    }
    
    /**
     * persists ForumPost Entity to the DB based on the given PostData
     * returns false if persist not possible
     * 
     * @param array $data
     */
    private function persistPost($data) {
                
        $thread = $this->getThread($data);
        
        //ignore posts without thread
        if (!$thread) {
            
            return false;
        }
        
        //set postdata
        $post = new \OSM\ComCollectBundle\Entity\ForumPost();        
        $post->setId($data['postId']);
        $post->setUserName($data['userName']);
        $post->setThread($thread);
        $post->setCreatedTime($data['createdTime']);
        $post->setContent($data['content']);
        $post->setNumber($data['number']);
        
        $this->entityManager->persist($post);
        $this->persistedPosts[] = $post;
    }
    
    /**
     * returns the ForumThread Entity by trying several methods
     * based on the given PostData
     * 
     * @param array $data
     * @return \Entity\ForumThread
     */
    private function getThread($data) {
        
        //thread exists in DB
        $thread = $this->getThreadById($data['threadId']);
        
        //thread has just been created
        if (!$thread AND isset($this->newThreads[$data['threadId']])) {
            
            $thread = $this->newThreads[$data['threadId']];
        }
        
        //post ist threadstarter
        if (!$thread AND $data['number'] == 1) {
            
            $thread = $this->makeNewThread($data);
            $this->entityManager->persist($thread);
        }
        
        //ignore posts without complete Threads
        if (!$thread) {

            return false;
        }
        
        return $thread;
    }
    
    /**
     * searches an existing ForumThread Entity in DB
     * 
     * @param integer $threadId
     * @return \Entity\ForumThread
     */
    private function getThreadById($threadId) {
        
        $thread = $this->entityManager->getRepository('OSMComCollectBundle:ForumThread');
        $thread = $thread->findOneBy(['id' => $threadId]);
        
        return $thread;
    }
    
    /**
     * creates and returns a new ForumThread Entity by given data
     * 
     * @param array $data
     * @return \Entity\ForumThread
     */
    private function makeNewThread($data) {
        
        $thread = new \OSM\ComCollectBundle\Entity\ForumThread();   
        $thread->setUserName($data['userName']);     
        $thread->setId($data['threadId']);     
        $thread->setBoardId($data['boardId']);
        $thread->setTitle($data['title']);
        
        $this->newThreads[$data['threadId']] = $thread;
        
        return $thread;
    }
    
    /**
     * sets the time of the last persisted post as collection time
     * 
     * (actual time could cause missing posts in following collection, 
     * since the forum feed is not always up-to-date)
     */
    private function setLastCollect() {
        
        if (empty($this->persistedPosts)) {
        
            return;
        }
        
        //get time of last persisted Post
        $lastPostTime = $this->getLatestCreatedTime();
        
        $repository = $this->entityManager->getRepository('OSMComCollectBundle:LastCollect');
        
        $lastCollect = $repository->findOneBy(['channel' => 'forum']);
        $lastCollect->setTime($lastPostTime);        
        
        $this->entityManager->persist($lastCollect);
    }
    
    /**
     * returns the latest createdTime of persisted ForumPost Entities
     * 
     * @return type
     */
    private function getLatestCreatedTime() {
        
        $latest = new \DateTime('01.01.1970');
        
        foreach ($this->persistedPosts as $post) {
            
            if ($latest < $post->getCreatedTime()) {
             
                $latest = $post->getCreatedTime();
            }
        }
        
        return $latest;
    }
}
