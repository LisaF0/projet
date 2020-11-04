<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\ProductsOrder;
use App\Entity\ShipAddresses;
use App\Form\ShipAddressesType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrdersController extends AbstractController
{
    /**
     * @Route("/commandes", name="orders_index")
     */
    public function index(): Response
    {
        return $this->render('orders/index.html.twig', [
            'controller_name' => 'OrdersController',
        ]);
    }

        /**
     * @Route("/shipAddForm", name="shipAdd_add")
     */
    public function addShipAdd(Request $request, EntityManagerInterface $manager)
    {

        $shipAddress = new ShipAddresses();
        $form = $this->createForm(ShipAddressesType::class, $shipAddress);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $shipAddress = $form->getData();
            $shipAddress->setUser($this->getUser());
            $manager->persist($shipAddress);
            $manager->flush();

            return $this->redirectToRoute('ordered');
        }

        return $this->render('orders/formShipAdd.html.twig', [
            'formShipAdd' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ordered", name="ordered")
     */
    public function ordered(SessionInterface $session, ProductsRepository $productRepository, EntityManagerInterface $manager)
    {
        $cart = $session->get('cart', []);

        $order = new Orders();
        $order->setUser($this->getUser());
        
        foreach($cart as $id => $quantity){
            $productsOrder = new ProductsOrder();
            $productsOrder->setOrder($order);
            $product = $productRepository->find($id);
            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $productsOrder->setProduct($product);
            $productsOrder->setQuantity($quantity);
            $order->addProductsOrder($productsOrder);
            $manager->persist($order);
        }

        $manager->flush();
        $session->clear();

        $this->addFlash('success', 'Votre commande a bien été effectuée');
        return $this->redirectToRoute('cart_index');
    }

}
