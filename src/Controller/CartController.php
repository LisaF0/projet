<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\DomainRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/cart", name="cart_show")
     * 
     * Fonction permettant d'afficher le panier
     * 
     * @param DomainRepository $dr
     * 
     * @return Response
     */
    public function showCart(DomainRepository $dr):Response
    {
        
        $incart = $this->cart->getIncart();

        //il faut réhydrater le produit afin de récupérer le nom de domaine
        foreach($incart as $cartLine){
            $domain = $dr->findOneById($cartLine['product']->getDomain()->getId());
            $cartLine['product']->setDomain($domain);
        }
        
        $total = $this->cart->getTotal($incart);

        return $this->render('cart/cart.html.twig', [
            'items' => $incart,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     * 
     * Fonction permettant d'ajouter un produit au panier
     * 
     * @param Request $request
     * @param Product $product
     * @param SessionInterface $session
     * 
     * @return Response
    */
    public function add(Request $request, Product $product = null, SessionInterface $session):Response
    {
        // Vérifier que le produit existe
        if($product){
            // On récupère la quantité du formulaire
            $qtt = $request->request->get("quantity");
            // On récupère la quantité de ce produit en stock
            $qttInStock = $product->getUnitStock();
            // On Vérifie que la quantité de produit ne dépasse pas la quantité en stock
            // Et qu'elle est supérieur à 0
            // Et que le produit est actif & disponible
            if($qtt <= $qttInStock && $qtt > 0 && $product->getActivate() && $product->getAvailable()){
                // On vérifie que le produit existe déjà dans le panier
                if(array_key_exists($product->getId(), $this->cart->getIncart())){
                    // On récupère la quantité de produit dans le panier
                    $qttInCart = $this->cart->getQuantityOfProduct($product);
                    // On vérifie que la quantité du formulaire + la quantité dans le panier
                    // ne dépasse pas la quantité du produit en stock
                    if($qtt + $qttInCart <= $qttInStock){
                        $this->cart->add($product, $qtt);
                        $session->set('cart', $this->cart);
                        $this->addFlash('success', 'Le produit a été ajouté au panier');
                    } 
                    else $this->addFlash('warning', 'La quantité de produit en stock est insuffisante 
                        par rapport à la quantité de produit que vous souhaitez ajouter au panier');
                }
                else{
                    $this->cart->add($product, $qtt);
                    $session->set('cart', $this->cart);
                    $this->addFlash('success', 'Le produit a été ajouté au panier');
                }
            }
            else $this->addFlash('warning', 'La quantité de produit en stock est insuffisante par 
                rapport à la quantité de produit que vous souhaitez ajouter au panier');
        }    
        else $this->addFlash('warning', 'Le produit n\'existe pas'); 
        return $this->redirectToRoute("products_show"); 
    }


    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     * 
     * Fonction permettant de supprimer un produit du panier
     * 
     * @param Product $product
     * @param SessionInterface $session
     * 
     * @return Response
     */
    public function remove(Product $product = null, SessionInterface $session):Response
    {
        // Vérifie que le produit existe
        // Et qu'il est bien dans le panier
        if($product && array_key_exists($product->getId(), $this->cart->getIncart())){
            $this->cart->remove($product);
            $session->set('cart', $this->cart);
            $this->addFlash('warning', 'Le produit a été supprimé du panier'); 
        }
        else $this->addFlash('warning', 'Le produit que vous souhaitez supprimer n\'existe pas');

        return $this->redirectToRoute("cart_show");
    }



    /**
     * @Route("/clearCart", name="cart_clear")
     * 
     * Fonction permettant de vider le panier
     * 
     * @return Response
     */
    public function clearCart():Response
    {
        $this->cart->clear($this->cart->getIncart());

        return $this->redirectToRoute('cart_show');
    }
}
