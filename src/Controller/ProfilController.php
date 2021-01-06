<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ShipAddress;
use App\Form\UserEmailType;
use App\Form\ShipAddressType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/infos", name="profil_infos")
     */
    public function infosUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getUser();
        // plus besoin de vérifier l'user grâce au { path: ^/profil, roles: ROLE_USER } dans security.yaml
        if($user){
            $addresses = $user->getShipAddresses();
            $formEmail = $this->createForm(UserEmailType::class, $user);
            $formEmail->handleRequest($request);
            if($formEmail->isSubmitted() && $formEmail->isValid()){
                $manager->flush();
                $this->addFlash('success', 'Votre email a bien été modifié');

                return $this->redirectToRoute("profil_infos");
            }
            $formPassword = $this->createForm(UserPasswordType::class, $user);
            $formPassword->handleRequest($request);
            if($formPassword->isSubmitted() && $formPassword->isValid()){
                // dd($user->getPassword());
                $validPassword = $passwordEncoder->isPasswordValid(
                    $user,
                    $formPassword->get('oldPlainPassword')->getData()
                );
                if($validPassword){
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $formPassword->get('newPlainPassword')->getData()
                        )
                        );
                    $manager->flush();
                    $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                }
                $this->addFlash('danger', 'Votre mot de passe ne correspond pas');
                return $this->redirectToRoute("profil_infos");
            }
            return $this->render('profil/informations.html.twig', [
                'user' => $user,
                'addresses' => $addresses,
                'formUserEmail' => $formEmail->createView(),
                'formUserPassword' => $formPassword->createView(),
            ]);  
        }
        return $this->redirectToRoute('app_login'); 
    }

    // /**
    //  * @Route("profil/addresses", name="profil_addresses")
    //  */
    // public function addressesUser(){
       
    //     $user = $this->getUser();
    //     if($user){
    //         $addresses = $user->getShipAddresses();
    //         return $this->render('profil/addresses.html.twig', [
    //             'addresses' => $addresses,
    //         ]);
    //     }
    //     return $this->redirectToRoute('app_login');  
    // }

    
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
     * @Route("/profil/editAddress/{id}", name="address_edit")
     */
    public function editAddress(ShipAddress $shipAddress = null, Request $request, EntityManagerInterface $manager)
    {
        // on vérifie : 
            //  l'adresse existe
            // user connecté correspond au user de l'adresse
            // que l'adresse n'a pas été utilisé pour une commande
        if(!$shipAddress || $this->getUser() !== $shipAddress->getUser() || count($shipAddress->getOrderings()) > 0){
            return $this->redirectToRoute('app_login');
        } else {
            $form = $this->createForm(ShipAddressType::class, $shipAddress);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $manager->flush();
                $this->addFlash('success', 'Cette adresse a bien été modifiée');
                return $this->redirectToRoute('profil_infos');
            }
            return $this->render('profil/editAddress.html.twig', [
                'formAddress' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/profil/deleteAddress/{id}", name="address_delete")
     */
    public function deleteAddress(ShipAddress $shipAddress = null, EntityManagerInterface $manager)
    {
        // on vérifie : 
            //  l'adresse existe
            // user connecté correspond au user de l'adresse
            // que l'adresse n'a pas été utilisé pour une commande
        if(!$shipAddress || $this->getUser() !== $shipAddress->getUser() || count($shipAddress->getOrderings()) > 0){
            return $this->redirectToRoute('app_login');
        } else {
            $manager->remove($shipAddress);
            $manager->flush();
            
            return $this->redirectToRoute('profil_addresses');
        }
    }

    /**
     * @Route("/profil/deleteAccount/{id}", name="account_delete")
     */
    public function deleteAccount(User $user = null, EntityManagerInterface $manager, SessionInterface $session)
    {

        if(!$user || $user !== $this->getUser()){
            return $this->redirectToRoute('app_login');
        } else {
            $orders = $user->getOrderings();
            foreach($orders as $order){
                $manager->remove($order);
            }

            $manager->remove($user);
            $manager->flush();
            $this->container->get('security.token_storage')->setToken(null);
            // on efface la session avant de rediriger l'user
            $this->addFlash('success', 'Votre compte utilisateur a bien été supprimé !');
            return $this->redirectToRoute('home_index'); 
        }
    }

    /**
     * @Route("/profil/addAddress", name="address_add")
     */
    public function addAddress(Request $request, EntityManagerInterface $manager)
    {
        $shipAddress = new ShipAddress();
        $form = $this->createForm(ShipAddressType::class, $shipAddress);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $shipAddress = $form->getData();
            $shipAddress->setUser($this->getUser());
            $manager->persist($shipAddress);
            $manager->flush();

            return $this->redirectToRoute('profil_infos');
            
        }

        return $this->render('cart/addShipAddress.html.twig', [
            'formAddShip' => $form->createView(),
        ]);
    }
}
