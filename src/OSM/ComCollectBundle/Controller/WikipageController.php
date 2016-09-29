<?php

namespace OSM\ComCollectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WikipageController extends Controller {
    
    public function collectAction() {
        
        $options = [
            'afterUpdateTime' => new \DateTime('01.01.2001'),
            'pattern_title' => '!Proposed features!i',
        ];
        
        $pages = $this->get('osmcc.wikipage.source')->setOptions($options)->get();        
        $this->get('osmcc.wikipage.persistence')->save($pages);
        
        return $this->render('OSMComCollectBundle:Default:wikipage.html.twig', ['pages' => $pages]);
    }
    
    private function getLastCollect() {
        
        $lastCollect = $this->getDoctrine()->getManager('osmcc')->getRepository('OSMComCollectBundle:LastCollect');
        $lastCollect = $lastCollect->findOneBy(['channel' => 'wikipage']);
        
        return $lastCollect;
    }
    
}
