<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\Promotions;
use App\Repository\ProductsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category, ProductsRepository $productsRepository,ManagerRegistry $managerRegistry, Request $request): Response
    {
        //On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);

        //On va chercher la liste des produits de la catégorie et les promotions
        $entityManager = $managerRegistry->getManager();
        $productsData = $productsRepository->findProductsPaginated($page, $category->getSlug(), 4);
        $products = $entityManager->getRepository(Products::class)->findAll();
        $promotions = $entityManager->getRepository(Promotions::class)->findAll();

        return $this->render('categories/list.html.twig', compact('category', 'productsData', 'products', 'promotions'));
    }
}