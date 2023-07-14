<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/produits", name="products_")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('products/index.html.twig', [
            'controller_name' => 'produit de l\utilisateur',
            
        ]);
    }

    /**
     * @Route("/{slug}", name="detail" )
     */
    public function details(Request $request): Response 
    {
        $products = new Products();
        return $this->render('products/details.html.twig', compact('product') );
    }

}