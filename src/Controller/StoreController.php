<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Product;
use App\Form\FilterType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StoreController extends AbstractController
{
    /**
     * @Route("/products", name="products_index")
     */
    public function index(Request $request, ProductRepository $pr): Response
    {
        $filter = New Filter();
        
        $formFilter = $this->createForm(FilterType::class, $filter);
        $formFilter->handleRequest($request);

        if($formFilter->isSubmitted() && $formFilter->isValid())
        {
            $products = $pr->findByFilter($filter);

        } else {
            $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        }
        return $this->render('store/index.html.twig', [
            'products' => $products,
            'formFilter' => $formFilter->createView()
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_detail")
     */
    public function detailProduct(Product $product = null): Response
    {
       return $this->render('store/product.html.twig', [
           'product' => $product    
       ]);
    }

}
