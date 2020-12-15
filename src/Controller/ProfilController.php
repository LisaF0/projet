<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil_show")
     */
    public function showProfil(User $user): Response
    {
        $addresses = $user->getShipAddresses();
        $orders = $user->getOrderings();
        
        

        return $this->render('profil/index.html.twig', [
            'addresses'  => $addresses,
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/editProfil/{id}", name="profil_edit")
     */
    public function editEmail(User $user, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Vos informations ont bien été modifiées');

            return $this->redirectToRoute("profil_show", [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('profil/editProfil.html.twig', [
            'formUser' => $form->createView()
        ]);
    }

    /**
     * @Route("/editAddress/{id}", name="address_edit")
     */
    public function editAddress(ShipAddress $shipAddress, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(ShipAddressType::class, $shipAddress);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($shipAddress);
            $manager->flush();
            $this->addFlash('success', 'Cette adresse a bien été modifiée');
            return $this->redirectToRoute('profil_show', [
                'id' => $this->getUser()->getId(),
            ]);
        }
        return $this->render('profil/editAddress.html.twig', [
            'formAddress' => $form->createView()
        ]);

    }

    /**
     * @Route("/deleteAddress/{id}", name="address_delete")
     */
    public function deleteAddress(ShipAddress $shipAddress, EntityManagerInterface $manager){
        $manager->remove($shipAddress);
        $manager->flush();
        
        return $this->redirectToRoute('profil_show', [
            'id' => $this->getUser()->getId(),
        ]);
    }


}
