<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="products_index")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
        ->getRepository(Product::class)
        ->findAll();

        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_detail")
     */
    public function detailProduct(Product $product = null): Response
    {
       return $this->render('products/product.html.twig', [
           'product' => $product
       ]);
    }
}