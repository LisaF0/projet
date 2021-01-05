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
     * @Route("/products/{sortBy}/{order}", name="products_index")
     */
    public function index($sortBy = null, $order = null, Request $request, ProductRepository $pr, PaginatorInterface $paginator): Response
    {
        //Secu à faire
        $filter = New Filter();
        $filter->page = $request->get('page', 1);
        $formFilter = $this->createForm(FilterType::class, $filter);
        $formFilter->handleRequest($request);
       
        $products = $pr->findByFilter($filter);
        
        // if($formFilter->isSubmitted() && $formFilter->isValid())
        // {
    
        //     $products = $pr->findByFilter($filter);

        // } else {
        //     if($sortBy == "name" || $sortBy == 'unitPrice' || $order == 'ASC' || $order == 'DESC'){
        //         $sortField = ($sortBy) ? $sortBy : "name";
        //         $sortOrder = ($order) ? $order : "ASC";
        //         $products = $this->getDoctrine()
        //         ->getRepository(Product::class)
        //         ->findBy([], [$sortField => $sortOrder]);
        //     } else {
        //         $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        //     }
            
        // }
        return $this->render('store/index.html.twig', [
            'products' => $products,
            'formFilter' => $formFilter->createView()
        ]);
    }
}
