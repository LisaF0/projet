<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Ordering;
use App\Form\ProductType;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * 
     * Fonction permettant d'afficher toute les commandes pour l'admin
     */
    public function index(OrderingRepository $or): Response
    {
        $orderings = $or->findAll();

        return $this->render('admin/index.html.twig', [
            'orders' => $orderings,
        ]);
    }
    /**
     * @Route("/admin/addProduct", name="add_product")
     * @Route("/admin/updateProduct/{id}", name="edit_product")
     * 
     * Fonction permettant à l'admin d'ajouter/editer un produit
     */
    public function addProduct(Product $product = null, Request $request, EntityManagerInterface $manager)
    {
        if(!$product){
            $product = new Product();
        }
        $formProduct = $this->createForm(ProductType::class, $product);
        $formProduct->handleRequest($request);
        if($formProduct->isSubmitted() && $formProduct->isValid()){
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('products_index');
        }
        return $this->render('admin/addProduct.html.twig',[
            'formProduct' => $formProduct->createView(),
            'editMode' => $product->getId() !== null ? $product : null
        ]);
    }

    /**
     * @Route("/admin/deleteProduct/{id}", name="delete_product")
     * 
     * Fonction permettant à l'admin de delete un produit
     */
    public function deleteProduct(Product $product = null, EntityManagerInterface $manager)
    {
        if($product){
            $manager->remove($product);
            $manager->flush();
        }
        return $this->redirectToRoute('products_index'); 
    }

    /**
     * @Route("/admin/statusToSend/{id}", name="statusToSend")
     * 
     * Fonction permettant à l'admin de modifier le statut de la commande
     */
    public function statusToSend(Ordering $ordering, EntityManagerInterface $manager)
    {
        if($ordering){
            $status = $ordering->getOrderingStatus();
            if($status == 1){
                $ordering->setOrderingStatus(3);
                $manager->flush();
                return $this->redirectToRoute('admin');
            }
        }
    }
}
