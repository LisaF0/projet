<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Cart;
use App\Entity\Facture;
use App\Entity\Product;
use App\Form\OrderType;
use App\Entity\Ordering;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use App\Entity\ProductOrdering;
use App\Repository\UserRepository;
use App\Repository\FactureRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
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
  public function chooseAddress(Request $request, EntityManagerInterface $manager, FactureRepository $fr, SessionInterface $session)
  {
    $incart = [];
    $user = $this->getUser();
    if(!$user){
      return $this->redirectToRoute("app_login");
    }
      
    $newOrder = new Ordering();
    $newFacture = new Facture();
    $cart = $session->get('cart', new Cart());
    //on récupère l'id du user uniquement
    $newFacture->setUserId($this->getUser()->getId());
    // set la new facture dans la new commande
    $newOrder->setFacture($newFacture);
    // afin de récupérer la dernière facture
    $lastFacture = $fr->findLastFacture($this->getUser()->getId());
    // pour pré remplir la nouvelle facture
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
      // set user dans order
      $newOrder->setUser($this->getUser());
      // set order dans la new facture
      $newOrder->getFacture()->setOrdering($newOrder);
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
        'reference' => $newOrder->getOrderingReference(),
      ]);
    }
    return $this->render('cart/addresses.html.twig', [
      'formOrder' => $formOrder->createView(),
    ]);
  }
  
  /**
   * @Route("/create-checkout-session/{reference}", name="create-checkout-session")
   */
  public function payment($reference, OrderingRepository $or, EntityManagerInterface $manager)
  {
    $order = $or->findOneByOrderingReference($reference);
  

    $YOUR_DOMAIN = 'http://127.0.0.1:8000';
    $productsForStripe = [];
    
    foreach($order->getProductOrderings()->getValues() as $cartLine){
      $productsForStripe[] = [
        'price_data' => [
          'currency' => 'eur',
          'product_data' => [
            'name' => $cartLine->getProduct()->getName(),
          ],
          'unit_amount' => $cartLine->getProduct()->getUnitPrice()*100,
        ],
        'quantity' => $cartLine->getQuantity(),
      ];
    }
    Stripe::setApiKey('sk_test_51HvgjELyEjuAwgbZtFkkq4UfxmsjafIAB10xIVuEjqHkQqVuHmrtBD4XvNGHPLnsOc7cKV8eL2lFxVNnVNSgyfpv00TCqiAFXL');
    $checkout_session = \Stripe\Checkout\Session::create([
      // 'customer_email' => $this->getUser()->getEmail(),
      'payment_method_types' => ['card'],
      'line_items' => [[
        $productsForStripe
      ]],
      'mode' => 'payment',
      'success_url' => $YOUR_DOMAIN.'/success/{CHECKOUT_SESSION_ID}',
      'cancel_url' => $YOUR_DOMAIN.'/error/{CHECKOUT_SESSION_ID}',
    ]);

    $order->setStripeSessionId($checkout_session->id);
    $manager->flush();
    return new JsonResponse(['id' => $checkout_session->id]);
  }

  /**
   * @Route("/success/{stripeSessionId}", name="success")
   */
  public function success($stripeSessionId, OrderingRepository $or, UserRepository $ur, SessionInterface $session, EntityManagerInterface $manager)
  {
    $order = $or->findOneByStripeSessionId($stripeSessionId);
    // on hydrate car order ne récup que l'id du user
    $user = $ur->findOneById($order->getUser()->getId());
    if(!$order || $user != $this->getUser()){
      return $this->redirectToRoute('home_index');
    }
    $total = $order->getTotal();
    $quantityTotal = $order->getQuantityTotal();
    //je passe le status de la commande à payé
    if($order->getOrderingStatus() == 0){
      $order->setOrderingStatus(1);
      $manager->flush();
      

      //je retire des stock la qte de produit qui a été vendu
      foreach($order->getProductOrderings() as $productLine){
        $product = $productLine->getProduct();
        $quantity = $productLine->getQuantity();
        $value = $product->getUnitStock() - $quantity;
        $product->setUnitStock($value);
      }
      $manager->flush();
      
      $cart = $session->get('cart', new Cart());
      //je vide le panier
      $cart->clear($cart->getFullCart());
      // return $this->render('checkout/success.html.twig', [
      //   'order' => $order,
      //   'total' => $total,
      //   'quantityTotal' => $quantityTotal,
      // ]);
    }
    return $this->render('checkout/success.html.twig', [
      'order' => $order,
      'total' => $total,
      'quantityTotal' => $quantityTotal,
      
    ]);
  }

  /**
   * @Route("/error/{CHECKOUT_SESSION_ID}", name="error")
   */
  public function error($stripeSessionId, OrderingRepository $or, UserRepository $ur, SessionInterface $session, EntityManagerInterface $manager)
  {
    $order = $or->findOneByStripeSessionId($stripeSessionId);
    $user = $ur->findOneById($order->getUser()->getId());
    if(!$order || $user != $this->getUser()){
      return $this->redirectToRoute('home_index');
    }

    if($order->getOrderingStatus() == 0){
      $order->setOrderingStatus(2);
      $manager->flush();
    }
    $this->addFlash('danger', 'Une erreur est survenue lors de votre paiement ou votre paiement a été refusé, veuillez réessayer');
    return $this->redirectToRoute('cart_index');
  }
}
