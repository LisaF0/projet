<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil_show")
     */
    public function showProfil(): Response
    {
        if($this->getUser()){
            return $this->render('profil/index.html.twig');
        }
        return $this->redirectToRoute('app_login');  
    }

    /**
     * @Route("/profil/infos", name="profil_infos")
     */
    public function infosUser(){
        if($this->getUser()){
            return $this->render('profil/informations.html.twig', [
                'user' => $this->getUser()
            ]);  
        }
        return $this->redirectToRoute('app_login'); 
    }

    /**
     * @Route("profil/addresses", name="profil_addresses")
     */
    public function addressesUser(){
       
        $user = $this->getUser();
        if($user){
            $addresses = $user->getShipAddresses();
            return $this->render('profil/addresses.html.twig', [
                'addresses' => $addresses,
            ]);
        }
        return $this->redirectToRoute('app_login');  
    }

    /**
     * @Route("/profil/orders", name="profil_orders")
     */
    public function ordersUser(){
        $user = $this->getUser();
        if($user){
           $orders = $user->getOrderings();
            return $this->render('profil/orders.html.twig', [
                'orders' => $orders,
            ]); 
        }
        return $this->redirectToRoute('app_login');  
    }

    /**
     * @Route("/editInfos/{id}", name="infos_edit")
     */
    public function editEmail(User $user, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, Request $request)
    {
        if($this->getUser() == $user){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $manager->flush();
                $this->addFlash('success', 'Vos informations ont bien été modifiées');

                return $this->redirectToRoute("profil_infos");
            }

            return $this->render('profil/editProfil.html.twig', [
                'formUser' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/editAddress/{id}", name="address_edit")
     */
    public function editAddress(ShipAddress $shipAddress, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() == $shipAddress->getUser()){
            $form = $this->createForm(ShipAddressType::class, $shipAddress);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $manager->flush();
                $this->addFlash('success', 'Cette adresse a bien été modifiée');
                return $this->redirectToRoute('profil_addresses');
            }
            return $this->render('profil/editAddress.html.twig', [
                'formAddress' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/deleteAddress/{id}", name="address_delete")
     */
    public function deleteAddress(ShipAddress $shipAddress, EntityManagerInterface $manager)
    {
        if($this->getUser() == $shipAddress->getUser()){
            $manager->remove($shipAddress);
            $manager->flush();
            
            return $this->redirectToRoute('profil_addresses');
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/deleteAccount/{id}", name="account_delete")
     */
    public function deleteAccount($id, UserRepository $ur, EntityManagerInterface $manager)
    {
        $user = $ur->findOneById($id);
        // $addresses = $user->getShipAddresses();
        // $orders = $user->getOrderings();
        // foreach($addresses as $address){
        //     // dd($address);
        //     $manager->remove($address);
        // }
        
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('home');
    }
}
