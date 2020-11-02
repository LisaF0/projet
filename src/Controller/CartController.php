<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(SessionInterface $session, ProductsRepository $productRepository)
    {
        $panier = $session->get('panier', []);

        $panierData = [];

        foreach($panier as $id => $quantity){
            $panierData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach($panierData as $item) {
            $totalItem = $item['product']->getUnitprice() * $item['quantity'];
            $total += $totalItem;
        }
        
        return $this->render('cart/index.html.twig', [
            'items' => $panierData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        //on récupère le panier de la session, sinon un tableau vide
        if(!empty($panier[$id])){
            //si j'ai déjà un produit avec cet identifiant dans le panier
            $panier[$id]++;
            //on rajoute l quantité du produit
        } else {
            // sinon la quantité est égale à 1
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);
        
        return $this->redirectToRoute("cart_index");
        
    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute("cart_index");
    }
}
