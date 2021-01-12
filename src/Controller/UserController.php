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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    // private $route;

    // public function __construct(SessionInterface $session){
    //     $this->route = $session->get('route', null);
    // }
    /**
     * @Route("/profil/infos", name="profil_infos")
     * 
     * Fonction permettant d'afficher le profil de l'utilisateur contenant son email, ses adresses, 
     * et la possibilité de changer son password et son email
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
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
            dd($formEmail->getData());
            // $manager->flush();
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
     * @param shipAddress $shipAddress
     * @param Request $request
     * @param EntityManagerInterface $manager
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
     */
    public function deleteAccount(User $user = null, EntityManagerInterface $manager)
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
            return $this->redirectToRoute('home_index'); 
        }
    }

    /**
     * @Route("/profil/addAddress", name="address_add")
     * 
     * Fonction permettant à l'utilisateur d'ajouter une adresse
     */
    public function addAddress(Request $request, EntityManagerInterface $manager, SessionInterface $session)
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
     */
    public function chooseAddress(Request $request, EntityManagerInterface $manager, FactureRepository $fr, SessionInterface $session, SerializerInterface $serializer)
    {
        // on stock la route
        $session->set('src',"choose_address");
        $incart = [];
        $user = $this->getUser();
        // if(!$user){
        //     return $this->redirectToRoute("app_login");
        // }
    
        $newOrder = new Ordering();
        $cart = $session->get('cart', new Cart());
        $newFacture = new Facture();

        // set la new facture dans la new commande + permet d'afficher la facture pré rempli
        $newOrder->setFacture($newFacture);
        // afin de récupérer la dernière facture
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
        
        foreach($cart->getFullCart() as $cartLine){
            $incart[] = [
            'product' => $cartLine['product'],
            'quantity' => $cartLine['quantity']
            ];
        }
        if(empty($incart)){
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
        $total = $cart->getTotal($incart);
        $formOrder = $this->createForm(OrderType::class, $newOrder);
        $formOrder->handleRequest($request);
        
        if($formOrder->isSubmitted() && $formOrder->isValid()){
            
            $jsonFacture = $serializer->serialize($newFacture, 'json'
            ,[AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']]
            );
            
            $session->set('facture', $jsonFacture);
            $newOrder->setFacture(null);
            $newOrder->setUser($this->getUser());

            // on persist à ce moment pour pouvoir addPorudctOrdering sur qq chose de "réel"
            $manager->persist($newOrder);
            foreach($cart->getFullCart() as $cartLine){
            $newProductOrder = new ProductOrdering();
            $product = $this->getDoctrine()->getRepository(Product::class)->find($cartLine['product']->getId());
            //set product To newProductOrder
            $newProductOrder->setProduct($product);
            //set quantity To newProductOrder
            $newProductOrder->setQuantity($cartLine['quantity']);
            //Hydrate newOrder avec le newProductOrder
            $newOrder->addProductOrdering($newProductOrder);
            $manager->persist($newProductOrder);
            }
            $manager->flush();
            return $this->render('checkout/index.html.twig', [
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