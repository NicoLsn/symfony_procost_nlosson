<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\UserProject;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProjectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        {
            $resolver->setDefaults([
                'data_class' => UserProject::class,
            ]);
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', NumberType::class, [
                'label' => 'Nombre d\'heures',
                'required' => true,
                'attr' =>array('min' => 1,),
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'label' => 'Projet concerné',
                'query_builder' => function (ProjectRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.deliveryDate IS NULL')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => true,
            ]);
    }
}