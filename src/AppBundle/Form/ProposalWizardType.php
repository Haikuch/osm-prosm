<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\StatusRepository;

class ProposalWizardType extends AbstractType
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
        $builder->add('description');
        $builder->add('createdTime', DateTimeType::class, ['date_widget' => 'single_text']);
        $builder->add('updatedTime', DateTimeType::class);
        $builder->add('draftStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy MM:ss']);
        $builder->add('rfcStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy MM:ss']);
        $builder->add('voteStartTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy MM:ss']);
        $builder->add('voteEndTime', DateTimeType::class, ['widget' => 'single_text', 'format' => 'dd.MM.yyyy MM:ss']);
        $builder->add('views');
        $builder->add('createdUserId');
        $builder->add('status', EntityType::class, $statusOptions);
        $builder->add('approved');
        $builder->add('archived');
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Proposal'
        ));
    }
}
