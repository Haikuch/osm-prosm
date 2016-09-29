<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Proposal;

/**
 * ProposalWizard controller.
 *
 */
class ProposalWizardController extends Controller {

    public function startAction(Request $request) { 
        
        #todo: here something like "ou have still an uncreated proposal" if found in DB
        
        //insert blank ProposalEntity to DB
        $proposal = new Proposal();
        $proposal->setIsCreating(true);
        $proposal->setAuthorId(666); #todo: auth        
        $em = $this->getDoctrine()->getEntityManager();        
        $em->persist($proposal);
        $em->flush();
        
        return $this->redirectToRoute('proposal_wizard_entry', ['request' => $request, 'proposal' => $proposal->getId()], 307);
    }
    
    public function entryAction(Request $request, Proposal $proposal) {
        
        $form = $this->createStepForm('entry', $proposal);
        $form->add('next', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() AND $form->isValid() AND $form->get('next')->isClicked()) {
            
            $em = $this->getDoctrine()->getEntityManager();        
            $em->persist($proposal);
            $em->flush();

            return $this->redirectToRoute('proposal_wizard_maintext', ['proposal' => $proposal->getId()]);
        }
        
        return $this->render('proposal/wizard/entry.html.twig', array(
            'proposal' => $proposal,
            'form' => $form->createView(),
        ));
    }

    public function maintextAction(Request $request, Proposal $proposal) {
        
        $form = $this->createStepForm('maintext', $proposal);
        $form->add('next', SubmitType::class);
        $form->add('previous', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() AND $form->isValid() AND $form->get('next')->isClicked()) {
            
            $em = $this->getDoctrine()->getEntityManager();        
            $em->persist($proposal);
            $em->flush();

            return $this->redirectToRoute('proposal_wizard_talk', ['proposal' => $proposal->getId()]);
        }

        else if ($form->isSubmitted() AND $form->get('previous')->isClicked()) {

            return $this->redirectToRoute('proposal_wizard_entry', ['proposal' => $proposal->getId()]);
        }

        return $this->render('proposal/wizard/maintext.html.twig', array(
            'proposal' => $proposal,
            'form' => $form->createView(),
        ));
    }

    public function talkAction(Request $request, Proposal $proposal) {
        
        $form = $this->createStepForm('talk', $proposal);
        $form->add('next', SubmitType::class);
        $form->add('previous', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() AND $form->isValid() AND $form->get('next')->isClicked()) {
            
            $em = $this->getDoctrine()->getEntityManager();        
            $em->persist($proposal);
            $em->flush();

            return $this->redirectToRoute('proposal_wizard_talk', ['proposal' => $proposal->getId()]);
        }

        else if ($form->isSubmitted() AND $form->get('previous')->isClicked()) {

            return $this->redirectToRoute('proposal_wizard_maintext', ['proposal' => $proposal->getId()]);
        }

        return $this->render('proposal/wizard/talk.html.twig', array(
            'proposal' => $proposal,
            'form' => $form->createView(),
        ));
    }
    
    protected function createStepForm($step, $proposal) {
        
        $formfields['entry'] = ['name', 'description'];
        $formfields['maintext'] = ['content'];
        $formfields['talk'] = [];
        
        $form = $this->createForm('AppBundle\Form\ProposalWizardType', $proposal, ['validation_groups' => [$step]]);
        
        //remove all nonvisible fields
        foreach ($form->all() as $fieldName => $data) {
            
            if (in_array($fieldName, $formfields[$step])) {
                
                continue;
            }
            
            $form->remove($fieldName);
        }
        
        return $form;
    }
}