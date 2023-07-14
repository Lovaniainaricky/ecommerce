<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/categories", name="categories_")
 */
class CategoriesController extends AbstractController
{
    
    /**
     * @Route("/{slug}", name="list" )
     */
    public function list(Categories $categorie): Response 
    {
        // $categorie = new Categories();
        // $categorie = [];

        //ON va aller chercher les produits de la catégorie
        $products = $categorie->getProducts();

        return $this->render('categories/list.html.twig', compact('categorie','products') );
        // return $this->render('categories/list.html.twig', [
        //     "categorie" => $categorie,
        //     "product" => $product,
        // ] );
    }

}