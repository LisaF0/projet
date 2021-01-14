<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Stripe\Stripe;

use Dompdf\Options;
use App\Entity\Cart;
use App\Entity\Facture;
use App\Repository\UserRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
  
    /**
     * @Route("/create-checkout-session/{reference}", name="create-checkout-session")
     * 
     * Fonction permettant à l'utilisateur d'afficher la page de paiement Stripe
     * 
     * @return Response
     */
    public function payment($reference, OrderingRepository $or, EntityManagerInterface $manager):Response
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
   * 
   * Fonction qui affiche la page de succès de commande à un utilisateur
   * 
   * @return Response
   */
  public function success($stripeSessionId, OrderingRepository $or,  SessionInterface $session, EntityManagerInterface $manager, Request $request, SerializerInterface $serializer):Response
  {
    
    $order = $or->findOneByStripeSessionId($stripeSessionId);
    if($this->getUser() != $order->getUser()){
      return $this->redirectToRoute('products_index');
    }
    $factureJson = $session->get('facture');
    $facture = $serializer->deserialize($factureJson, Facture::class, 'json');
    $facture->setOrdering($order);
    $order->setFacture($facture);
    $manager->persist($facture);
    $manager->persist($order);
    $manager->flush();
    
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
      $cart->clear();
      return $this->render('checkout/success.html.twig', [
        'order' => $order,
        'total' => $total,
        'quantityTotal' => $quantityTotal,
      ]);
    }
  }

  /**
   * @Route("/downloadPDF/{id}", name="dl_facture")
   * 
   * Fonction qui permet de download la facture
   * 
   * @return Response
   */
  public function dlPDF($id, OrderingRepository $or):Response
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
    $html = $this->renderView('checkout/facture.html.twig', [
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

    return new Response('', 200, [  'Content-Type' => 'application/pdf',]);
  }

  /**
   * @Route("/error/{stripeSessionId}", name="error")
   * 
   * Fonction qui affiche une page d'erreur suite au paiement de Stripe
   * 
   * @param OrderingRepository $or
   * @param UserRepository $ur
   * @param EntityManagerInterface $manager
   * 
   * 
   * @return Response
   */
  public function error($stripeSessionId, OrderingRepository $or, UserRepository $ur, EntityManagerInterface $manager):Response
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
    $this->addFlash('danger', 'Une erreur est survenue lors de votre paiement, veuillez réessayer');

    return $this->render('checkout/error.html.twig');
  }

}
