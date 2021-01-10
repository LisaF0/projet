<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Product;
use App\Form\FilterType;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
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
     */
    public function index(Request $request, ProductRepository $pr, PaginatorInterface $paginator): Response
    {
        //Secu à faire
        $filter = New Filter();
        $filter->page = $request->get('page', 1);
        $formFilter = $this->createForm(FilterType::class, $filter);
        $formFilter->handleRequest($request);
        $productsActive = $pr->findByFilterAndActivate($filter);
        $allProducts = $pr->findByFilter($filter);
        
        return $this->render('store/index.html.twig', [
            'allProducts' => $allProducts,
            'productsActive' => $productsActive,
            'formFilter' => $formFilter->createView()
        ]);
    }
}
