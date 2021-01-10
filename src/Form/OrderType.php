<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Facture;
use App\Entity\Ordering;
use App\Entity\ShipAddress;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShipAddressRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    private $security;

    public function __construct(ShipAddressRepository $repo, Security $security)
    {
        $this->security = $security;
        $this->repo = $repo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder

            ->add('shipAddress', EntityType::class, [
                'class' => ShipAddress::class,
                'choices' => $this->repo->findByUser($this->security->getUser()),
                'label' => 'Choisir une adresse de livraison : ',
                'constraints' => new NotNull([
                    'message' => 'Veuillez choisir une adresse de livraison.'
                ])
            ])
            ->add('Facture', FactureType::class, [
                'label' => 'Choisir une adresse de facturation :',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                    'message' => 'Veuillez remplir une adresse de facturation.'
                    ]),
                    new NotNull([
                        'message' => 'Veuillez choisir une adresse de facturation.'
                    ])    

                ]
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Passer au paiement'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ordering::class,
        ]);
    }
}
