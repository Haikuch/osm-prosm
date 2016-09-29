<?php

namespace OSM\ComCollectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WikitalkController extends Controller
{
    
    public function collectAction()
    {
        $pages = $this->get('osmcc.wikitalk.source')->getNew($this->getLastCollect()->getTime());        
        $this->get('osmcc.wikitalk.persistence')->save($pages);
        
        return $this->render('OSMComCollectBundle:Default:wikitalk.html.twig', ['pages' => $pages]);
    }
    
    private function getLastCollect() {
        
        $lastCollect = $this->getDoctrine()->getManager('osmcc')->getRepository('OSMComCollectBundle:LastCollect');
        $lastCollect = $lastCollect->findOneBy(['channel' => 'wikitalk']);
        
        return $lastCollect;
    }
    
}
