<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OSM\ComCollectBundle\Entity\ForumThread;

use AppBundle\Entity\Proposal;

/**
 * Proposal controller.
 *
 */
class TalkController extends Controller {
    
    public function allAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $stmt = $em->getConnection()->prepare("
        
            (SELECT 'wikitalk' AS channel, content, created_time AS createdTime, user_name AS userName FROM osmcc.wikitalk_posts wikitalk
             WHERE wikitalk.thread_id = :wikitalkThreadId)
             
             UNION
             
            (SELECT 'forum' AS channel, content, created_time as createdTime, user_name as userName FROM osmcc.forum_posts forum
             WHERE forum.thread_id = :forumThreadId)
              
            ORDER BY createdTime DESC
        
        ");
        
        $params['wikitalkThreadId'] = 1353;
        $params['forumThreadId'] = 54759;
        
        $stmt->execute($params);
        
        $posts = $stmt->fetchAll();
        
        $data = [   'proposal' => $proposal, 
                    'posts' => $posts,
                ];
        
        return $this->render('proposal/talk/all.html.twig', $data);
    }

    
    public function forumIndexAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $threadRepo = $em->getRepository('OSMComCollectBundle:ForumThread');
        $threads = $threadRepo->findBy(['id' => [54759]]); #54948, 54961, 54971
        
        //direct forward when only one thread is tracked
        if (count($threads) == 1) {
            
            $routeData = [   'id' => $proposal->getId(), 
                        'thread' => $threads[0]->getId(), 
                        'title' => str_replace(' ', '_', $threads[0]->getTitle())
                    ];
            
            return $this->redirectToRoute('talk_forum_thread', $routeData);
        }
        
        $data = [   'proposal' => $proposal, 
                    'threads' => $threads,
                ];
        
        return $this->render('proposal/talk/forumIndex.html.twig', $data);
    }  
    
    public function forumThreadAction(Proposal $proposal, ForumThread $thread) { 
        
        $lastvisit = new \DateTime('26.06.2016, 16:40');
        
        $data = [   'proposal' => $proposal, 
                    'thread' => $thread,
                    'lastvisit' => $lastvisit,
                ];
        
        return $this->render('proposal/talk/forum.html.twig', $data);
    }    
    

    
    public function wikitalkIndexAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $threadRepo = $em->getRepository('OSMComCollectBundle:WikitalkThread');
        $page = $threadRepo->findByPageName('Talk:Proposed_features/Extending_kneipp_water_cure');
        
        //direct forward when only one thread is tracked
        if (count($page) == 1 OR TRUE) {
            
            $routeData = [      'id'        => $proposal->getId(), 
                                'page'    => 666, 
                                'title'     => 'wiki'
                    ];
            
            return $this->redirectToRoute('talk_wikitalk_page', $routeData);
        }
        
        $data = [   'proposal' => $proposal, 
                    'pages' => $page,
                ];
        
        return $this->render('proposal/talk/wikitalkIndex.html.twig', $data);
    }  

    public function wikitalkPageAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $threadRepository = $em->getRepository('OSMComCollectBundle:WikitalkThread');
        $wikitalkThreads = $threadRepository->findByPageName('Talk:Proposed_features/Extending_kneipp_water_cure');
        
        $lastvisit = new \DateTime('10.06.2016, 16:40');
        
        $data = [   'pagename' => 'Proposed_features/Extending_kneipp_water_cure',
                    'proposal' => $proposal, 
                    'threads' => $wikitalkThreads,
                    'lastvisit' => $lastvisit,
                ];
        
        return $this->render('proposal/talk/wikitalk.html.twig', $data);
    }

    
    public function mailinglistIndexAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $threadRepo = $em->getRepository('OSMComCollectBundle:MailinglistThread');
        $threads = $threadRepo->findBy(['id' => [79]]);
        
        //direct forward when only one thread is tracked
        if (count($threads) == 1) {
            
            $routeData = [      'id'        => $proposal->getId(), 
                                'thread'    => $threads[0]->getId(), 
                                'title'     => str_replace(' ', '_', $threads[0]->getTitle())
                    ];
            
            return $this->redirectToRoute('talk_mailinglist_thread', $routeData);
        }
        
        $data = [   'proposal' => $proposal, 
                    'threads' => $threads,
                ];
        
        return $this->render('proposal/talk/mailinglistIndex.html.twig', $data);
    }  

    public function mailinglistThreadAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $threadRepository = $em->getRepository('OSMComCollectBundle:MailinglistThread');
        $mailinglistThread = $threadRepository->findOneById(79);
        
        $lastvisit = new \DateTime('10.06.2016, 16:40');
        
        $data = [   'proposal' => $proposal, 
                    'thread' => $mailinglistThread,
                    'lastvisit' => $lastvisit,
                ];
        
        return $this->render('proposal/talk/mailinglist.html.twig', $data);
    }

}
