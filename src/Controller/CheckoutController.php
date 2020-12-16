<?php

namespace App\Controller;

use Stripe\Stripe;

use App\Entity\Cart;
use App\Repository\UserRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create-checkout-session")
     */
    public function index($reference, OrderingRepository $or, EntityManagerInterface $manager)
    {
      $YOUR_DOMAIN = 'http://127.0.0.1:8000';
      $productsForStripe = [];
      $order = $or->findOneByOrderingReference($reference);
      
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
          'customer_email' => $this->getUser()->getEmail(),
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
        return $this->render('checkout/success.html.twig', [
          'order' => $order,
          'total' => $total,
          'quantityTotal' => $quantityTotal,
        ]);
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
    public function error()
    {
      return $this->render('checkout/error.html.twig', []);
    }
}
