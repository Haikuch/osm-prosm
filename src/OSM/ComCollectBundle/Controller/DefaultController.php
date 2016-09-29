<?php

namespace OSM\ComCollectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OSMComCollectBundle:Default:index.html.twig');
    }
}
