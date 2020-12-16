<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Facture;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\Ordering;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use App\Entity\ProductOrdering;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(SessionInterface $session)
    {
        $cart = $session->get('cart', new Cart());
        // $incart = [];
        
        foreach($cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }
        $total = $cart->getTotal($incart);

        return $this->render('cart/index.html.twig', [
            'items' => $incart,
            'total' => $total,
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

            return $this->redirectToRoute('choose_address');
        }

        return $this->render('cart/addShipAddress.html.twig', [
            'formAddShip' => $form->createView(),
        ]);
    }

    /**
     * @Route("/chooseAdd", name="choose_address")
     */
    public function buy(SessionInterface $session, Request $request, EntityManagerInterface $manager)
    {
        $newOrder = new Ordering();
        $newFacture = new Facture();
        $newOrder->setFacture($newFacture);
        $cart = $session->get('cart', new Cart());

        foreach($cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }
       
        $total = $cart->getTotal($incart);
        $formSA = $this->createForm(OrderType::class, $newOrder);
        $formSA->handleRequest($request);
        if($formSA->isSubmitted() && $formSA->isValid()){
            $newOrder->setUser($this->getUser());
            $newOrder->getFacture()->setOrdering($newOrder);
            $manager->persist($newOrder);
            
            foreach($cart->getFullCart() as $cartLine){
                
                $newProductOrder = new ProductOrdering();
                $product = $this->getDoctrine()->getRepository(Product::class)->find($cartLine['product']->getId());
                $newProductOrder->setProduct($product);
                $newProductOrder->setQuantity($cartLine['quantity']);
                
                $newOrder->addProductOrdering($newProductOrder);
                $manager->persist($newProductOrder);
            }
            $manager->flush();
            
            
            return $this->render('checkout/index.html.twig', [
                'items' => $incart,
                'total' => $total,
                'order' => $newOrder,
                'reference' => $newOrder->getOrderingReference(),
            ]);
        }
        return $this->render('cart/addresses.html.twig', [
            'formSA' => $formSA->createView(),
        ]);
    }
}
