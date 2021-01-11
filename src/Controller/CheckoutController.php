<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Stripe\Stripe;

use Dompdf\Options;
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
   * @Route("/downloadPDF/{id}", name="dl_facture")
   */
  public function dlPDF($id, OrderingRepository $or)
  {
    $order = $or->findOneById($id);
    // $user = $ur->findOneById($order->getUser()->getId());
    $total = $order->getTotal();
    $quantityTotal = $order->getQuantityTotal();
    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    
    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('checkout/success.html.twig', [
      'order' => $order,
      'total' => $total,
      'quantityTotal' => $quantityTotal,
      'title' => "Votre Facture"
    ]);
    
    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("Facture".$order->getFacture()->getFactureReference(), [
        "Attachment" => true
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
