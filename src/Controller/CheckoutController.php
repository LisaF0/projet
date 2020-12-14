<?php

namespace App\Controller;

use Stripe\Stripe;

use App\Entity\Cart;
use App\Entity\Ordering;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create-checkout-session")
     */
    public function index($reference, OrderingRepository $or)
    {
      // $cart = $session->get('cart', new Cart());
      // $order = $manager->getRepository(Ordering::class)->findOneByReference($reference);
      $order = $or->findOneByReference($reference);
      $productsForStripe = [];
      

      // foreach($cart->getFullCart() as $cartLine){
        foreach($order->getProductOrderings()->getValues() as $cartLine){
          // dd($cartLine);
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
      $checkoutSession = \Stripe\Checkout\Session::create([
              'payment_method_types' => ['card'],
              'line_items' => [[
                  $productsForStripe
              ]],
              'mode' => 'payment',
              'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
              'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
              ]);
              
      return new JsonResponse(['id' => $checkoutSession->id]);
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
