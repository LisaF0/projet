<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(SessionInterface $session, ProductsRepository $productRepository)
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach($cart as $id => $quantity){
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach($cartWithData as $item) {
            $totalItem = $item['product']->getUnitprice() * $item['quantity'];
            $total += $totalItem;
        }
        dump($cartWithData);
        
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);
        //on récupère le cart de la session, sinon un tableau vide
        if(!empty($cart[$id])){
            //si j'ai déjà un produit avec cet identifiant dans le panier
            $cart[$id]++;
            //on rajoute l quantité du produit
        } else {
            // sinon la quantité est égale à 1
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);

        $this->addFlash('success', 'Le produit a été ajouté au panier');
        
        return $this->redirectToRoute("products_index");
        
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        $this->addFlash('warning', 'Le produit a été supprimé du panier');

        return $this->redirectToRoute("cart_index");
    }

    
    /**
     * @Route("ordered", name="ordered")
     */
    public function ordered(SessionInterface $session, ProductsRepository $productRepository)
    {
        $cart = $session->get('cart', []);
        foreach($cart as $id => $quantity){
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        dd($cartWithData);
    }


}
