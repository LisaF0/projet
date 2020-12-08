<?php

namespace App\Controller;

use Stripe\Product;
use App\Entity\Cart;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', new Cart());
        $incart = [];
        foreach($cart->getFullCart() as $cartLine){
            $incart[] = [
                'product' => $cartLine['product'],
                'quantity' => $cartLine['quantity']
            ];
        }
        $total = $cart->getTotal($incart);

        $stripe = new \Stripe\StripeClient(
            'sk_test_51HvgjELyEjuAwgbZtFkkq4UfxmsjafIAB10xIVuEjqHkQqVuHmrtBD4XvNGHPLnsOc7cKV8eL2lFxVNnVNSgyfpv00TCqiAFXL'
          );


          $stripe->checkout->sessions->create([
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'payment_method_types' => ['card'],
            'line_items' => [
              [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Vin"
                    ] ,
                    'unit_amount' => $total*100,
                ],
                
                'quantity' => 1,
              ],
            ],
            'mode' => 'payment',
          ]);
        return new JsonResponse([ 'id' => $session->getId() ]);
    }

    /**
     * @Route("/create-checkout-session", name="checkout")
     */
    public function checkout()
    {
        \Stripe\Stripe::setApiKey('sk_test_51HvgjELyEjuAwgbZtFkkq4UfxmsjafIAB10xIVuEjqHkQqVuHmrtBD4XvNGHPLnsOc7cKV8eL2lFxVNnVNSgyfpv00TCqiAFXL');
       

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                  'name' => 'T-shirt',
                ],
                'unit_amount' => 2000,
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

        return new JsonResponse([ 'id' => $session->id ]);

    }

    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return $this->render('checkout/success.html.twig', []);
    }

    /**
     * @Route("/error", name="error")
     */
    public function error()
    {
        return $this->render('checkout/error.html.twig', []);
    }
}
