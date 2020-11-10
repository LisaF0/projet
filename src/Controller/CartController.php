<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Form\UserType;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\ShipAddress;
use App\Entity\ProductOrder;
use App\Form\ShipAddressType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository, Request $request, EntityManagerInterface $manager)
    {
        $cart = $session->get('cart', new Cart());
        //On récupère le panier en session
        $cartWithData = [];
        
        $newOrder = new Order();
        // On créer une commande
        foreach($cart->getContent() as $cartLine){
            // Pour chaque produit dans le panier
            $cartWithData[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
            // On rempli un tableau key->value avec le produit et la quantité
            $productOrder = new ProductOrder();
            // On créer un nouvel ligne de commande
            $productOrder->setProduct($cartLine['product']);
            // On insert le produit
            $productOrder->setQuantity($cartLine['quantity']);
            // Et sa quantité
            $newOrder->addProductOrder($productOrder);
            // On ajoute la nouvelle ligne de commande à la commande
        }

        $total = 0;

        foreach($cartWithData as $item) {
            // Pour chaque item du panier
            $totalItem = $item['product']->getUnitprice() * $item['quantity'];
            // On récupère le prix total
            $total += $totalItem;
        }
      
        $newOrder->setUser($this->getUser());
        // On récupère l'utilisateur

        $formSA = $this->createForm(OrderType::class, $newOrder);
        $formSA->handleRequest($request);

        
        // dump($newOrder);
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
            'formSA' => $formSA->createView(),
        ]);
    }

    /**
    * @Route("/cart/add/{id}", name="cart_add")
    */
    public function add(Product $product, SessionInterface $session)
    {

        $cart = $session->get('cart', new Cart());
        $cart->add($product);
        $session->set('cart', $cart);

        $this->addFlash('success', 'Le produit a été ajouté au panier');
        
        return $this->redirectToRoute("products_index");
        
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, SessionInterface $session)
    {
        $cart = $session->get('cart', new Cart());
        // dd($cart);
        $cart->remove($product);
        $session->set('cart', $cart);

        $this->addFlash('warning', 'Le produit a été supprimé du panier');

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/ShipAddress/add", name="shipAdd_add")
     */
    public function addShipAddress(Request $request, EntityManagerInterface $manager ){
        $shipAddress = new ShipAddress();
        $form = $this->createForm(ShipAddressType::class, $shipAddress);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $shipAddress = $form->getData();
            $shipAddress->setUser($this->getUser());
            $manager->persist($shipAddress);
            $manager->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cart/addShipAddress.html.twig', [
            'formAddShip' => $form->createView(),
        ]);
    }


}
