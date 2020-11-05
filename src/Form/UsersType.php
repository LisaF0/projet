<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\ShipAddresses;
use App\Repository\UsersRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\ShipAddressesRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UsersType extends AbstractType
{
    private $ShipAddressesRepository;
    private $security;
    public function __construct(ShipAddressesRepository $ShipAddressesRepository, Security $security)
    {
        $this->ShipAddressesRepository = $ShipAddressesRepository;
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // dd($this->security->getUser());
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            // ->add('shipAddresses', ChoiceType::class, [
            //     'choice_value' => 'firstname',
            //     'choice_attr' => function(?ShipAddresses $shipAddresses){
            //         // return $shipAddresses ? $shipAddresses->getFirstname() : '';
            //         return $shipAddresses->getFirstname(); 
            //     }
            // ]) 

            // ->add('shipAddresses', EntityType::class, [
            //     'class' => ShipAddresses::class,
            //     'choice_label' => 'firstname'             
            // ]) 
            ->add('author', EntityType::class, [
                'class' => ShipAddresses::class,
                'choice_label' => 'firstname',
                'choices' => $this->ShipAddressesRepository->findByUser($this->security->getUser())
            ])
       

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
