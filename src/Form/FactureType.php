<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter votre nom de famille',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Votre nom de famille ne peut pas être plus long que 50 caractères',
                    ]),
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter votre prénom',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Votre prénom ne peut pas être plus long que 50 caractères' 
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter votre ville',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Votre ville ne peut pas être plus long que 50 caractères' 
                    ])
                ]
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez compléter votre code postal',
                    ]),
                    new Length([
                        'max' => 5,
                        'min' => 5,
                        'minMessage' => 'Votre code postal ne peut pas être plus petit que 5 caractères',
                        'maxMessage' => 'Votre code postal ne peut pas être plus long que 5 caractères' 
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'constraints' => new NotBlank([
                    'message' => 'Veuillez compléter votre adresse',
                ])
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
