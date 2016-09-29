<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Proposal;

/**
 * Proposal controller.
 *
 */
class TestController extends Controller {
    
    /**
     * Lists all Proposal entities.
     *
     */
    public function markdownAction() {        
        
        return $this->render('test/markdown.html.twig');
    }
}
