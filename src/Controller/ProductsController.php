<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products", name="products_")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Products $products): Response
    {
        return $this->render('products/index.html.twig', [
            'controller_name' => 'produit de l\utilisateur',
            'products' => $products,
        ]);
    }

    /**
     * @Route("/{slug}", name="detail" , methods={"GET"})
     */
    public function details(): Response 
    {
        // dd($products);
        return $this->render('products/details.html.twig', [
            'controller_name' => 'produit de l\utilisateur',
        ]);
    }

}