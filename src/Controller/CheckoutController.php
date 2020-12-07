<?php

namespace App\Controller;

use App\Entity\Cart;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
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
