<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\StatusRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProposalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $statusOptions['class'] = 'AppBundle:Status';
        $statusOptions['choice_translation_domain'] = 'status';
        $statusOptions['query_builder'] = 
            function (StatusRepository $status) {
                
                return $status->createQueryBuilder('s')->where('s.id IN (1,2,3)');
            };
        
        $builder->add('name');
        
        $builder->add('content');
        $builder->add('description', TextType::class);
        $builder->add('draftStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy']);
        $builder->add('rfcStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy']);
        $builder->add('voteStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy']);
        $builder->add('voteEndTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy']);
        
        $builder->add('isDraft', CheckboxType::class, ['mapped' => false]);
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Proposal'
        ));
    }
}
