<?php

namespace App\Form;

use App\Entity\Profession;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date D\'embauche',
                'required' => true,
            ])
            ->add('cost', NumberType::class, [
                'label' => 'Coût horaire',
                'required' => true,
                'attr' =>array('min' => 1,),
            ])
            ->add('profession', EntityType::class, [
                'class' => Profession::class,
                'choice_label' => 'name',
                'required' => true,
    ]);
    }
}