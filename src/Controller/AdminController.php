<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Ordering;
use App\Form\ProductType;
use App\Repository\DomainRepository;
use App\Repository\OrderingRepository;
use App\Repository\ProductOrderingRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * 
     * Fonction permettant d'afficher toute les commandes pour l'admin
     */
    public function index(OrderingRepository $or, Request $request, PaginatorInterface $paginator, ProductRepository $pr, DomainRepository $dr): Response
    {
        
        $donnees = $or->findAll();
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
        // dd($array);
        return $this->render('admin/index.html.twig', [
            'orders' => $orderings,
            'productMostSold' => $array
        ]);
    }
    /**
     * @Route("/admin/addProduct", name="add_product")
     * @Route("/admin/updateProduct/{id}", name="edit_product")
     * 
     * Fonction permettant à l'admin d'ajouter/editer un produit
     */
    public function addProduct(Product $product = null, Request $request, EntityManagerInterface $manager, ProductOrderingRepository $por)
    {
        if(!$product){
            $product = new Product();
        }
        // On vérifie que le produit n'appartient pas déjà à une commande
        if($por->findByProductId($product->getId())){
            $this->addFlash('warning', 'Ce produit ne peut pas être modifié');
            return $this->redirectToRoute('products_index');
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
    public function deleteProduct(Product $product = null, EntityManagerInterface $manager, ProductOrderingRepository $por)
    {
        if($product){
            if($por->findByProductId($product->getId())){
                $this->addFlash('warning', 'Ce produit ne peut pas être supprimé');
                return $this->redirectToRoute('products_index');
            }
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('success', 'Le produit a bien été supprimé');
        }
        return $this->redirectToRoute('products_index'); 
    }

    /**
     * @Route("/admin/desactiveProduct/{id}", name="desactive_product")
     * 
     * Fonction permettant à l'admin d'activer ou de désactiver un produit
     */
    public function desactivate(Product $product, EntityManagerInterface $manager)
    {
        $activeState = $product->getActivate() ? false : true;
        $product->setActivate($activeState);
        $manager->flush();

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
