<?php

namespace OSM\ComCollectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ForumController extends Controller {
    
    public function collectAction() {
        
        $posts = $this->get('osmcc.forum.source')->getNew($this->getLastCollect()->getTime());       
        $posts = $this->get('osmcc.forum.persistence')->save($posts);
        
        return $this->render('OSMComCollectBundle:Default:forum.html.twig', ['posts' => $posts]);
    }
    
    private function getLastCollect() {
        
        $lastCollect = $this->getDoctrine()->getEntityManager('osmcc')->getRepository('OSMComCollectBundle:LastCollect');
        $lastCollect = $lastCollect->findOneBy(['channel' => 'forum']);
        
        return $lastCollect;
    }
}
