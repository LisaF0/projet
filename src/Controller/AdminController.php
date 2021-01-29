<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Ordering;
use App\Form\ProductType;
use App\Repository\DomainRepository;
use App\Repository\OrderingRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ProductOrderingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/orders", name="show_orders")
     * 
     * Fonction permettant d'afficher toute les commandes pour l'admin
     * 
     * @param OrderingRepository $or
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ProductRepository $pr
     * @param DomainRepository $dr
     * 
     * @return Response
     */
    public function showOrders(OrderingRepository $or, Request $request, PaginatorInterface $paginator, ProductRepository $pr, DomainRepository $dr):Response
    {
        
        $donnees = $or->findByPaid();
        $orderings = $paginator->paginate(
            $donnees, // Requête qui contient les données
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nb de résultat par page
        );
        $productMostSold = $pr->findMostSold(); // [[]] on récupère le ou les produits les plus vendu, ainsi que la quantité qui a été vendu 
        $array = [];
        foreach($productMostSold as $productLine){
            //on récupère le produit par son nom
            $product = $pr->findOneByName($productLine['name']);
            // on récupère l'id du domaine
            $domainId = $product->getDomain()->getId();
            // et récupère le nom du domaine
            $domain = $dr->findOneById($domainId);
            
            $array[] = [
                'productName' => $productLine['name'],
                'domain' => $domain->getName(),
                'quantity' => array_pop($productLine)
            ];
        }
        return $this->render('admin/orders.html.twig', [
            'orders' => $orderings,
            'productMostSold' => $array
        ]);
    }
    
    /**
     * @Route("/admin/addProduct", name="add_product")
     * @Route("/admin/updateProduct/{id}", name="edit_product")
     * 
     * Fonction permettant à l'admin d'ajouter/editer un produit
     * 
     * @param Product $product
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ProductOrderingRepository $por
     * 
     * @return Response
     */
    public function addProduct(Product $product = null, Request $request, EntityManagerInterface $manager, ProductOrderingRepository $por):Response
    {
        if(!$product){
            $product = new Product();
        }
        // On vérifie que le produit n'appartient pas déjà à une commande
        if($por->findByProductId($product->getId())){
            $this->addFlash('warning', 'Ce produit ne peut pas être modifié');
            return $this->redirectToRoute('products_show');
        }
        $formProduct = $this->createForm(ProductType::class, $product);
        $formProduct->handleRequest($request);
        if($formProduct->isSubmitted() && $formProduct->isValid()){
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('products_show');
        }
        return $this->render('admin/addProduct.html.twig',[
            'formProduct' => $formProduct->createView(),
            'editMode' => $product->getId() !== null ? $product : null
        ]);
    }

    /**
     * @Route("/admin/deleteProduct", name="delete_product")
     * 
     * Fonction permettant à l'admin de delete un produit
     * 
     * @param Product $product
     * @param EntityManagerInterface $manager
     * @param ProductOrderingRepository $por
     * 
     * @return Response
     */
    public function deleteProduct(Request $request, EntityManagerInterface $manager, ProductOrderingRepository $por):Response
    {
       // On vérifie que la requête est valide
        if(!$request->query->get("id")){
            $this->addFlash('warning', 'Requête non valide');
        } else {
            // on récupère le produit avec l'ID
            $product = $manager->getRepository(Product::class)->findOneBy(['id' => $request->query->get("id")]);
            //si le produit n'existe pas ou si le produit a déjà été commandé
            if(!$product || $por->findByProductId($product->getId())){
                $this->addFlash('warning', 'Ce produit ne peut pas être supprimé ou n\'existe pas');
            } else {
                $manager->remove($product);
                $manager->flush();
                $this->addFlash('success', 'Le produit a bien été supprimé');
            }
        }
        return $this->redirectToRoute('products_show'); 
    }

    /**
     * @Route("/admin/desactiveProduct/{id}", name="desactive_product")
     * 
     * Fonction permettant à l'admin d'activer ou de désactiver un produit
     * 
     * @param Product $product
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function desactivate(Product $product = null, EntityManagerInterface $manager):Response
    {
        if(!$product){
            $this->addFlash('danger', 'Ce produit n\'existe pas');
        } else {
            $activeState = $product->getActivate() ? false : true;
            $product->setActivate($activeState);
            $manager->flush();
        }

        return $this->redirectToRoute('products_show');
    }

    /**
     * @Route("/admin/statusToSend/{id}", name="statusToSend")
     * 
     * Fonction permettant à l'admin de modifier le statut de la commande
     * 
     * @param Ordering $ordering
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function statusToSend(Ordering $ordering, EntityManagerInterface $manager):Response
    {
        if($ordering){
            $status = $ordering->getOrderingStatus();
            if($status == 1){
                $ordering->setOrderingStatus(3);
                $manager->flush();
            }
        }
        return $this->redirectToRoute('admin');
    }
}
