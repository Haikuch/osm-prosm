<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Ajax controller.
 *
 */
class AjaxController extends Controller {   
    
    //parse markup to markdown
    public function parseMarkup2MarkdownAction(Request $request) {
        
        $markdown = $this->get('markdown')->up2down($request->request->get('markup'));
        
        return new \Symfony\Component\HttpFoundation\Response($markdown);
    }
    
}
