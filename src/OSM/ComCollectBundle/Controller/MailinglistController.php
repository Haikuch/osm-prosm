<?php

namespace OSM\ComCollectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MailinglistController extends Controller
{
    
    public function collectAction(){
        
        $posts = $this->get('osmcc.mailinglist.source')->getNew(10);
        $this->get('osmcc.mailinglist.persistence')->save($posts);
        
        #there is still sometime message id key conflicts, and it seems that mails are ignored when fetched more than 10
        
        return $this->render('OSMComCollectBundle:Default:mailinglist.html.twig');
    }
}
