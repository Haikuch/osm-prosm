<?php

namespace OSM\ComCollectBundle\Utils;

use Doctrine\ORM\EntityManager;

use OSM\ComCollectBundle\Utils\MailinglistSource;
use OSM\ComCollectBundle\Entity\MailinglistThread;

class MailinglistPersistence {    
           
    /**
     * doctrine EM instance
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    private $mailinglistSource;
    
    /**
     * the persisted MailinglistPost Entities
     * @var array 
     */
    private $persistedPosts;
    
    /**
     * initializes the EntityManager
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, MailinglistSource $mailinglistSource ) {
        
        $this->entityManager = $entityManager;
        $this->mailinglistSource = $mailinglistSource;
    }
    
    public function save($datas) {
        
        $dataIds = [];
        foreach ($datas as $data) {
            
            $dataIds[] = $data['mailId'];
            
            $post = $this->getPostByData($data);        
            $thread = $this->getThread($data);

            if (!$thread) {

                continue;
            }
            
            $post->setThread($thread);
            
            $this->persistedPosts[$post->getMessageId()] = $post;
            
            $this->entityManager->persist($post);
        }
            
        //update last collect time
        $this->entityManager->persist($this->getLastCollect()->setTime(new \DateTime('NOW')));
        
        $this->entityManager->flush();
        
        //mark mails as read in the mailbox
        $this->mailinglistSource->setMailsAsRead($dataIds);
    }
    
    private function getPostByData($data) {
        
        $post = new \OSM\ComCollectBundle\Entity\MailinglistPost();   
        $post->setContent($data['textPlain']); #TODO: mailinglist information cut be trimmed
        $post->setFromName($data['fromName']);
        $post->setFromAddress($data['fromAddress']);
        $post->setFromTime(new \DateTime($data['date'])); //->date uses the script timezone
        $post->setCollectedTime(new \DateTime('now'));
        $post->setMessageId($data['messageId']);
        $post->setInreplytoId($data['inreplytoId']);
        
        return $post;
    }
       
    
    /**
     * returns the MailinglistThread Entity by trying several methods
     * based on the given PostData
     * 
     * @param array $data
     * @return \Entity\MailinglistThread
     */
    private function getThread($data) {
        
        $thread = false;
        
        //get thread from parent post in DB
        if ($data['inreplytoId']) {
        
            $thread = $this->getThreadByMessageId($data['inreplytoId']);
        }
        
        //make new thread
        #TODO: there is still magic needed for orphaned mails
        if (!$thread AND !$data['inreplytoId']) {
            
            $thread = $this->makeNewThread($data);
            $thread ? $this->entityManager->persist($thread) : '';
        }
        
        //if all methods fail then return
        if (!$thread) {
            
            return false;
        }
        
        return $thread;
    }
    
    private function getThreadByMessageId($messageId) {
        
        //thread has just been created
        if (isset($this->persistedPosts[$messageId])) {
            
            return $this->persistedPosts[$messageId]->getThread();
        }
        
        //try to find thread in DB
        $repo = $this->entityManager->getRepository('OSMComCollectBundle:MailinglistPost');
        $post = $repo->findOneByMessageId($messageId);
        
        if (!$post) {
            
            return false;
        }
        
        return $post->getThread();
    }
    
    private function makeNewThread($data) {
        
        $thread = new MailinglistThread();
        $thread->setTitle($data['title']);
        
        $list = $this->getListBySlug($data['slug']);
        
        if (!$list) {
            
            return false;
        }
        
        $thread->setList($list);
        
        return $thread;
    }
    
    
    private function getLastCollect() {
        
        $lastCollect = $this->entityManager->getRepository('OSMComCollectBundle:LastCollect');
        $lastCollect = $lastCollect->findOneBy(['channel' => 'mailinglist']);
        
        return $lastCollect;
    }
    
    private function getListBySlug($slug) {
        
        $repo = $this->entityManager->getRepository('OSMComCollectBundle:MailinglistList');
        $list = $repo->findOneBySlug($slug);
        
        if (!$list) {
            
            return false;
        }
        
        return $list;
    }
    
}
