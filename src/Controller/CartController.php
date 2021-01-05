<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cart;

    public function __construct(SessionInterface $session){
        $this->cart = $session->get('cart', new Cart());
    }
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index()
    {
        $incart = [];
        
        foreach($this->cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }

        $total = $this->cart->getTotal($incart);

        return $this->render('cart/index.html.twig', [
            'items' => $incart,
            'total' => $total,
        ]);
    }

    /**
    * @Route("/cart/add/{id}", name="cart_add")
    */
    public function add(Request $request, Product $product = null, SessionInterface $session)
    {
        // Vérifier que le produit existe
        if(!$product){
            $this->addFlash('warning', 'Le produit n\'existe pas');
            return $this->redirectToRoute("products_index"); 
        }
        $qtt = $request->request->get("quantity");
        // Vérifier que la quantité de produit de dépasse pas la quantité en stock et qu'elle est supérieur à 0
    
        if($qtt <= $product->getUnitStock() && $qtt > 0){

            $this->cart->add($product, $qtt);
            $session->set('cart', $this->cart);
    
            $this->addFlash('success', 'Le produit a été ajouté au panier');
            
            return $this->redirectToRoute("products_index");
        } else {
            $this->addFlash('warning', 'La quantité du produit demandé est supérieur à celle du stock');
            return $this->redirectToRoute("products_index"); 
        }
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product = null, SessionInterface $session)
    {
        if(!$product){
            $this->addFlash('warning', 'Le produit que vous souhaitez supprimer n\'existe pas');

            return $this->redirectToRoute("cart_index");
        }
        $this->cart->remove($product);
        $session->set('cart', $this->cart);

        $this->addFlash('warning', 'Le produit a été supprimé du panier');

        return $this->redirectToRoute("cart_index");
    }



    /**
     * @Route("clearCart", name="cart_clear")
     */
    public function clearCart(){
        $incart = [];
        
        foreach($this->cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }
        $this->cart->clear($incart);

        return $this->redirectToRoute('cart_index');
    }
}
