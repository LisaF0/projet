<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products_index")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
        ->getRepository(Products::class)
        ->findAll();

        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_detail")
     */
    public function detailProduct(Products $product = null): Response
    {
       return $this->render('products/product.html.twig', [
           'product' => $product
       ]);
    }
}