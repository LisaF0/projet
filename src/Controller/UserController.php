<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Facture;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\Ordering;
use App\Entity\ShipAddress;
use App\Form\UserEmailType;
use App\Form\ShipAddressType;
use App\Form\UserPasswordType;
use App\Entity\ProductOrdering;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    
    /**
     * @Route("/profil/infos", name="profil_infos")
     * 
     * Fonction permettant d'afficher le profil de l'utilisateur contenant son email, ses adresses, 
     * et la possibilité de changer son password et son email
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * 
     * @return Response
     */
    public function infosUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session): Response
    {
        $session->set('src',"profil_infos");
        
        
        $user = $this->getUser();
        $addresses = $user->getShipAddresses();
        //création du formulaire d'email de l'user
        $formEmail = $this->createForm(UserEmailType::class, $user);
        $formEmail->handleRequest($request);
        
        // Modifier l'email de l'user
        if($formEmail->isSubmitted() && $formEmail->isValid()){
            $manager->flush();
            $this->addFlash('success', 'Votre email a bien été modifié');
            return $this->redirectToRoute("profil_infos");
        }
        //création du formulaire de modification du password de l'utilisateur
        $formPassword = $this->createForm(UserPasswordType::class, $user);
        $formPassword->handleRequest($request);
        if($formPassword->isSubmitted() && $formPassword->isValid()){
            // Vérif de l'ancien password
            $validPassword = $passwordEncoder->isPasswordValid(
                $user,
                $formPassword->get('oldPlainPassword')->getData()
            );
            //vérif du new password & modification
            if($validPassword){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $formPassword->get('newPlainPassword')->getData()
                    )
                );
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute("profil_infos");
            }
            $this->addFlash('danger', 'Votre mot de passe ne correspond pas');
            return $this->redirectToRoute("profil_infos");
        }
        return $this->render('user/profilInfos.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'formUserEmail' => $formEmail->createView(),
            'formUserPassword' => $formPassword->createView(),
        ]);  
    }

    /**
     * @Route("/profil/orders", name="profil_orders")
     * 
     * Fonction permettant d'afficher l'historique des commandes de l'utilisateur
     * 
     * @return Response
     */
    public function ordersUser():Response
    {
        $user = $this->getUser();
        $orders = $user->getOrderings();
        return $this->render('user/profilOrders.html.twig', [
            'orders' => $orders,
        ]);   
    }   

    /**
     * @Route("/profil/editAddress/{id}", name="address_edit")
     * 
     * Fonction permettant à l'utilisateur de modifier l'une de ses adresses si elle n'a pas été utilisé pour une commande
     * 
     * @param ShipAddress $shipAddress
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function editAddress(ShipAddress $shipAddress = null, Request $request, EntityManagerInterface $manager):Response
    {
        // on vérifie : 
            //  l'adresse existe
            // user connecté correspond au user de l'adresse
            // que l'adresse n'a pas été utilisé pour une commande
        if(!$shipAddress || $this->getUser() !== $shipAddress->getUser() || count($shipAddress->getOrderings()) > 0){
            return $this->redirectToRoute('app_login');
        } else {
            
            //création du formulaire d'adresse de livraison
            $form = $this->createForm(ShipAddressType::class, $shipAddress);
            $form->handleRequest($request);
            //modification de l'adresse
            if($form->isSubmitted() && $form->isValid()){
                dd($form->getData());
                $manager->flush();
                $this->addFlash('success', 'Cette adresse a bien été modifiée');
                return $this->redirectToRoute('profil_infos');
            }
            return $this->render('user/editAddress.html.twig', [
                'formAddress' => $form->createView()
            ]);
        }
    }
    

    /**
     * @Route("/profil/deleteAddress/{id}", name="address_delete")
     * 
     * Fonction permettant à l'utilisateur de supprimer l'une de ses adresses si elle n'a pas été utilisé pour une commande
     * 
     * @param ShipAddress $shipAddress
     * @param EntityManagerInterface
     * 
     * @return Response
     */
    public function deleteAddress(ShipAddress $shipAddress = null, EntityManagerInterface $manager):Response
    {
        // on vérifie : 
            //  l'adresse existe
            // user connecté correspond au user de l'adresse
            // que l'adresse n'a pas été utilisé pour une commande
        if(!$shipAddress || $this->getUser() !== $shipAddress->getUser() || count($shipAddress->getOrderings()) > 0){
            return $this->redirectToRoute('app_login');
        } else {
            
            //supression de l'adresse
            $manager->remove($shipAddress);
            $manager->flush();
            $this->addFlash('success', 'Votre adresse a bien été supprimé');
            
            return $this->redirectToRoute('profil_infos');
        }
    }


    /**
     * @Route("/profil/deleteAccount/{id}", name="account_delete")
     * 
     * Fonction permettant à l'utilisateur de supprimer son compte
     * 
     * @param User $user
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function deleteAccount(User $user = null, EntityManagerInterface $manager):Response
    {

        if(!$user || $user !== $this->getUser()){
            return $this->redirectToRoute('app_login');
        } else {
            //on récupère toutes les commandes de l'utilisateur
            $orders = $user->getOrderings();
            // on supprime toutes les commandes de l'utilisateur
            foreach($orders as $order){
                $manager->remove($order);
            }
            // on supprime l'utilisateur
            $manager->remove($user);
            $manager->flush();
            $this->container->get('security.token_storage')->setToken(null);
            // on efface la session avant de rediriger l'user
            $this->addFlash('success', 'Votre compte utilisateur a bien été supprimé !');
            return $this->redirectToRoute('home'); 
        }
    }

    /**
     * @Route("/profil/addAddress", name="address_add")
     * 
     * Fonction permettant à l'utilisateur d'ajouter une adresse
     * 
     * @param Request $request
     * @param EntityManager $manager
     * @param SessionInterface $session
     * 
     * @return Response
     */
    public function addAddress(Request $request, EntityManagerInterface $manager, SessionInterface $session):Response
    {
        // on récupère la route précédente stocké en session
        $route = $session->get('src');
        $shipAddress = new ShipAddress();
        $form = $this->createForm(ShipAddressType::class, $shipAddress);
        $form->handleRequest($request);


        
        if($form->isSubmitted() && $form->isValid()){
            $shipAddress = $form->getData();
            $shipAddress->setUser($this->getUser());
            $manager->persist($shipAddress);
            $manager->flush();
            $this->addFlash('success', 'Vous avez ajoutez une nouvelle adresse');
            
            //rediriger d'où l'on vient
            return $this->redirectToRoute($route);
        }
        
        return $this->render('user/addShipAddress.html.twig', [
            'formAddShip' => $form->createView(),
        ]);
    }    



    /**
     * @Route("/chooseAdd", name="choose_address")
     * @IsGranted("ROLE_USER")
     * 
     * Fonction permettant à l'utilisateur de choisir son adresse de livraison 
     * et de facturation pour sa commande avant le paiement
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FactureRepository $fr
     * @param SessionInterface $session
     * @param SerializerInterface $serializer
     * 
     * @return Response
     */
    public function chooseAddress(Request $request, EntityManagerInterface $manager, FactureRepository $fr, SessionInterface $session, SerializerInterface $serializer):Response
    {
        // on stock la route
        $session->set('src',"choose_address");
        
        $user = $this->getUser();
        
        $cart = $session->get('cart', new Cart());
        $incart = $cart->getIncart();
        
        $newOrder = new Ordering();
        $newFacture = new Facture();
        $newFacture->setUserId($user->getId());
        // set la nouvelle facture dans la nouvelle commande 
        $newOrder->setFacture($newFacture);
        // On récupère la dernière facture de l'utilisateur
        $lastFacture = $fr->findLastFacture($user->getId());
        // pour pré remplir la nouvelle facture si elle existe
        if($lastFacture){
            $newFacture->setUserId($lastFacture->getUserId());
            $newFacture->setFirstname($lastFacture->getFirstname());
            $newFacture->setLastname($lastFacture->getLastname());
            $newFacture->setCity($lastFacture->getCity());
            $newFacture->setZipcode($lastFacture->getZipcode());
            $newFacture->setAddress($lastFacture->getAddress());
        }
        
        $formOrder = $this->createForm(OrderType::class, $newOrder);
        $formOrder->handleRequest($request);
        
        if($formOrder->isSubmitted() && $formOrder->isValid()){
            // on encode la facture
            $jsonFacture = $serializer->serialize(
                $newFacture, 
                'json',[AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']]
            );
            // on stock la facture en session
            $session->set('facture', $jsonFacture);
            //on met la facture de la nouvelle commande à null
            $newOrder->setFacture(null);
            // on récupère l'user qu'on stock dans la facture
            $newOrder->setUser($this->getUser());
            
            // on persist à ce moment pour pouvoir addProductOrdering 
            $manager->persist($newOrder);

            // on récupère ce qu'il y a dans le panier
            // pour l'ajouter à la commande
            foreach($cart->getFullCart() as $cartLine){
                $newProductOrder = new ProductOrdering();
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($cartLine['product']
                    ->getId());
                $newProductOrder->setProduct($product);
                $newProductOrder->setQuantity($cartLine['quantity']);
                $newOrder->addProductOrdering($newProductOrder);
                $manager->persist($newProductOrder);
            }
            $manager->flush();
            
            //on vérifie que le panier n'est pas vide
            if(empty($incart)){
                return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
            }
            $total = $cart->getTotal($incart);
            return $this->render('checkout/recap.html.twig', [
                'items' => $incart,
                'total' => $total,
                'order' => $newOrder,
                'facture' => $newFacture,
                'reference' => $newOrder->getOrderingReference(),
            ]);
        }
        return $this->render('user/chooseAddresses.html.twig', [
            'formOrder' => $formOrder->createView(),
        ]);
    }
}
