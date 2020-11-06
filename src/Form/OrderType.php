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
        // dd($this->security->getUser());
        $builder
            // ->add('email')
            // ->add('roles')
            // ->add('password')
            // ->add('shipAddress', ChoiceType::class, [
            //     'choice_value' => 'firstname',
            //     'choice_attr' => function(?ShipAddress $shipAddress){
            //         // return $shipAddress ? $shipAddress->getFirstname() : '';
            //         return $shipAddress->getFirstname(); 
            //     }
            // ]) 

            // ->add('shipAddress', EntityType::class, [
            //     'class' => ShipAddress::class,
            //     'choice_label' => 'firstname'             
            // ]) 
            ->add('ShipAddress', EntityType::class, [
                'class' => ShipAddress::class,
                
                'choices' => $this->ShipAddressRepository->findByUser($this->security->getUser())
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
