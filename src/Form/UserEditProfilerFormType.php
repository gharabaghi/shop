<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserEditProfilerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Length(['max' => 150])
            ]
        ])
        ->add(
            'name',
            TextType::class,
            [
                'constraints' => [new Length(['min' => 10, 'max' => 100])]
            ]
        )
        ->add('birthDate', DateType::class, [
            'widget' => 'single_text',
        ])
        ->add('address', TextType::class, [
            'constraints' => [
                new Length(['min' => 20, 'max' => 200])
            ]
        ])
        ->add('postalCode', TextType::class, [
            'constraints' => [
                new Length(['min' => 10, 'max' => 100])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
