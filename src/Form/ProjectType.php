<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        {
            $resolver->setDefaults([
                'data_class' => Project::class,
            ]);
        }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('sellingPrice', NumberType::class, [
                'label' => 'Prix de vente',
                'required' => true,
                'attr' => array('min' => 1,),
            ]);
    }
}