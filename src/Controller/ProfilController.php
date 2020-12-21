<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use App\Repository\ShipAddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil_show")
     */
    public function showProfil(SessionInterface $session): Response
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
        $user = $this->getUser();
        if($user){
            return $this->render('profil/informations.html.twig', [
                'user' => $user
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
     * @Route("/profil/editInfos/{id}", name="infos_edit")
     */
    public function editEmail(User $user = null, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, Request $request)
    {
        if(!$user || $this->getUser() !== $user){
            return $this->redirectToRoute('app_login');
        } else {
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
    }

    /**
     * @Route("/profil/editAddress/{id}", name="address_edit")
     */
    public function editAddress(ShipAddress $shipAddress = null, Request $request, EntityManagerInterface $manager)
    {
        if(!$shipAddress || $this->getUser() !== $shipAddress->getUser()){
            
            return $this->redirectToRoute('app_login');
        } else {
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
    }

    // /**
    //  * @Route("/profil/deleteAddress/{id}", name="address_delete")
    //  */
    // public function deleteAddress(ShipAddress $shipAddress = null, EntityManagerInterface $manager)
    // {
    //     if(!$shipAddress || $this->getUser() !== $shipAddress->getUser()){
    //         return $this->redirectToRoute('app_login');
    //     } else {
    //         $manager->remove($shipAddress);
    //         $manager->flush();
            
    //         return $this->redirectToRoute('profil_addresses');
    //     }
    // }

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
            return $this->redirectToRoute('app_login'); 
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

            return $this->redirectToRoute('profil_addresses');
            
        }

        return $this->render('cart/addShipAddress.html.twig', [
            'formAddShip' => $form->createView(),
        ]);
    }
}
