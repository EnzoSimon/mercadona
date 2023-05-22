<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\Promotions;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits', name: 'products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $products = $entityManager->getRepository(Products::class)->findAll();
        $promotions = $entityManager->getRepository(Promotions::class)->findAll();
        $categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'promotions' => $promotions,
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Products $product): Response
    {
        return $this->render('products/details.html.twig', compact('product'));
    }
}