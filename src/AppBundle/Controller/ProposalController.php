<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Proposal;

/**
 * Proposal controller.
 *
 */
class ProposalController extends Controller
{
    /**
     * Lists all Proposal entities.
     *
     */
    public function indexAction()
    {        
        $em = $this->getDoctrine()->getManager();

        $proposals = $em->getRepository('AppBundle:Proposal')->findAll();

        return $this->render('proposal/index/index.html.twig', array(
            'proposals' => $proposals,
        ));
    }

    public function newAction() { 
        
        return $this->render('proposal/new/new.html.twig', array());
    }

    /**
     * Finds and displays a Proposal entity.
     *
     */
    public function showAction(Proposal $proposal) {
        
        return $this->render('proposal/show/show.html.twig', array(
            'proposal' => $proposal,
        ));
    }

    public function talkAction(Proposal $proposal) {
        
        $em = $this->getDoctrine()->getManager('osmcc');
        
        $forumPostRepo = $em->getRepository('OSMComCollectBundle:ForumPost');
        $forumPosts = $forumPostRepo->findBy(['threadId' => 54954]);
        
        $forumThreadRepo = $em->getRepository('OSMComCollectBundle:ForumThread');
        $forumThread = $forumThreadRepo->findOneBy(['id' => 54954]);
        
        
        $data = [   'proposal' => $proposal, 
                    'forumPosts' => $forumPosts, 
                    'forumThread' => $forumThread,
                ];
        
        return $this->render('proposal/talk/forum.html.twig', $data);
    }

    /**
     * Displays a form to edit an existing Proposal entity.
     *
     */
    public function editAction(Request $request, Proposal $proposal)
    {
        $form = $this->createForm('AppBundle\Form\ProposalType', $proposal);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proposal);
            $em->flush();

            return $this->redirectToRoute('proposal_edit', array('id' => $proposal->getId()));
        }
        
        #err($form->getErrors());

        return $this->render('proposal/edit/edit.html.twig', array(
            'proposal' => $proposal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Proposal entity.
     *
     */
    public function deleteAction(Request $request, Proposal $proposal)
    {
        $form = $this->createDeleteForm($proposal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proposal);
            $em->flush();
        }

        return $this->redirectToRoute('proposal_index');
    }

    /**
     * Creates a form to delete a Proposal entity.
     *
     * @param Proposal $proposal The Proposal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proposal $proposal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proposal_delete', array('id' => $proposal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
