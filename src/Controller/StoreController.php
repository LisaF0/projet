<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StoreController extends AbstractController
{
    /**
     * @Route("/products/", name="products_index")
     * 
     * Fonction permettant d'afficher la liste des produits, et de les filtrer
     * 
     * @return Response
     */
    public function showProducts(Request $request, ProductRepository $pr): Response
    {
        $filter = New Filter();
        $filter->page = $request->get('page', 1);
        $formFilter = $this->createForm(FilterType::class, $filter);
        $formFilter->handleRequest($request);
        if($formFilter->isSubmitted() && $formFilter->isValid()){
            // Produit pour l'utilisateur
            $productsActive = $pr->findByFilterAndActivate($filter);
            // Produit pour l'administrateur
            $allProducts = $pr->findByFilter($filter);
        }
        $productsActive = $pr->findByFilterAndActivate($filter);
        $allProducts = $pr->findByFilter($filter);
        
        return $this->render('store/index.html.twig', [
            'allProducts' => $allProducts,
            'productsActive' => $productsActive,
            'formFilter' => $formFilter->createView()
        ]);
    }
}
