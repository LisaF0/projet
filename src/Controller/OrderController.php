<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\ChooseAddType;
use App\Entity\ProductOrder;
use App\Entity\ShipAddress;
use App\Form\ShipAddressType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShipAddressRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/commandes", name="order_index")
     */
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    /**
     * @Route("/shipAddForm", name="shipAdd_add")
     */
    public function addShipAdd(Request $request, EntityManagerInterface $manager)
    {

        $shipAddress = new ShipAddress();
        $form = $this->createForm(ShipAddressType::class, $shipAddress);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $shipAddress = $form->getData();
            $shipAddress->setUser($this->getUser());
            $manager->persist($shipAddress);
            $manager->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('order/formShipAdd.html.twig', [
            'formShipAdd' => $form->createView(),
        ]);
    }

    /**
     * @Route("/shipChooseForm/", name="shipAdd_choose")
     */
    public function chooseShipAdd(Request $request)
    {
        $order = new Order();
        $user = $this->getUser();
        $order->setUser($user);
 
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        // if($form->isSubmitted() && $form->isValid()){

        //     $order->setShipAddress($chooseAdd);
        //     return $this->redirectToRoute('ordered');
        // }

        return $this->render('order/formChooseAdd.html.twig', [
            'formUser' => $form->createView(),
            'user' => $user
        ]);
    }

}
