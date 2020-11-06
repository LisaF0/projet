<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\ShipAddress;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShipAddressRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderType extends AbstractType
{
    private $ShipAddressRepository;
    private $security;
    public function __construct(ShipAddressRepository $ShipAddressRepository, Security $security)
    {
        $this->ShipAddressRepository = $ShipAddressRepository;
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('ShipAddress', EntityType::class, [
                'class' => ShipAddress::class,
                'choices' => $this->ShipAddressRepository->findByUser($this->security->getUser())
            ])
            // ->add('Facture', EntityType::class, [
            //     'class' => Facture::class,

            // ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
