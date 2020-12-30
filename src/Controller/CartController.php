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
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function add(Request $request, Product $product, SessionInterface $session)
    {
        // Vérifier qu'il y'en ai en stock et que le produit existe
        $qtt = $request->request->get("quantity");
        $this->cart->add($product, $qtt);
        $session->set('cart', $this->cart);

        $this->addFlash('success', 'Le produit a été ajouté au panier');
        
        return $this->redirectToRoute("products_index");
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, SessionInterface $session)
    {
        $this->cart->remove($product);
        $session->set('cart', $this->cart);

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
    public function buy(Request $request, EntityManagerInterface $manager, FactureRepository $fr)
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute("app_login");
        }
        $newOrder = new Ordering();
        $newFacture = new Facture();
        $newFacture->setUserId($this->getUser()->getId());
        $newOrder->setFacture($newFacture);
        // remplir les données de l'adresse de facturation à partir de la facture précédente
        
        $lastFacture = $fr->findLastFacture($this->getUser()->getId());
        if($lastFacture){
            
            $newFacture->setUserId($lastFacture->getUserId());
            $newFacture->setFirstname($lastFacture->getFirstname());
            $newFacture->setLastname($lastFacture->getLastname());
            $newFacture->setCity($lastFacture->getCity());
            $newFacture->setZipcode($lastFacture->getZipcode());
            $newFacture->setAddress($lastFacture->getAddress());

        }
        

        foreach($this->cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }
       
        $total = $this->cart->getTotal($incart);
        $formOrder = $this->createForm(OrderType::class, $newOrder);
        $formOrder->handleRequest($request);
        if($formOrder->isSubmitted() && $formOrder->isValid()){
            $newOrder->setUser($this->getUser());
            $newOrder->getFacture()->setOrdering($newOrder);
            $manager->persist($newOrder);
            
            foreach($this->cart->getFullCart() as $cartLine){
                
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
            'formOrder' => $formOrder->createView(),
        ]);
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
