<?php

namespace App\Form;

use App\Entity\ShipAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ShipAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'constraints' => new Length([
                    'min' => 5,
                    'max' => 5,
                    'exactMessage' => 'Le code postal doit contenir 5 caractères.',
                ])
                
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShipAddress::class,
        ]);
    }
}
