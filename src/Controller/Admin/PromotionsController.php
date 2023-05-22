<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Entity\Promotions;
use App\Entity\PromotionsFormType;
use App\Repository\ProductsRepository;
use App\Repository\PromotionsRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class PromotionsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $produits = $productsRepository->findAll();
        return $this->render('admin/products/index.html.twig', compact('produits'));
    }

    #[Route('/promotion_add/{id}', name: 'promotion_add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $promotion = new Promotions();

        // On crée le formulaire
        $promotionForm = $this->createForm(PromotionsFormType::class, $promotion);

        // On traite la requête du formulaire
        $promotionForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($promotionForm->isSubmitted() && $promotionForm->isValid()){
            
            // On génère le slug
            $slug = $slugger->slug($promotion->getDiscount());
            $promotion->setSlug($slug);

            // On stocke
            $em->persist($promotion);
            $em->flush();

            $this->addFlash('success', 'Promotion ajoutée au produit');

            // On redirige
            return $this->redirectToRoute('admin_products_index');
        }


        // return $this->render('admin/products/add.html.twig',[
        //     'promotionForm' => $promotionForm->createView()
        // ]);

        return $this->renderForm('admin/products/add.html.twig', compact('promotionForm'));
        // ['promotionForm' => $promotionForm]
    }

    // #[Route('/edition/{id}', name: 'edit')]
    // public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    // {
    //     // On vérifie si l'utilisateur peut éditer avec le Voter
    //     $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

    //     // On divise le prix par 100
    //     // $prix = $product->getPrice() / 100;
    //     // $product->setPrice($prix);

    //     // On crée le formulaire
    //     $promotionForm = $this->createForm(ProductsFormType::class, $product);

    //     // On traite la requête du formulaire
    //     $promotionForm->handleRequest($request);

    //     //On vérifie si le formulaire est soumis ET valide
    //     if($promotionForm->isSubmitted() && $promotionForm->isValid()){
    //         // On récupère les images
    //         $images = $promotionForm->get('images')->getData();

    //         foreach($images as $image){
    //             // On définit le dossier de destination
    //             $folder = 'products';

    //             // On appelle le service d'ajout
    //             $fichier = $pictureService->add($image, $folder, 300, 300);

    //             $img = new Images();
    //             $img->setName($fichier);
    //             $product->addImage($img);
    //         }
            
            
    //         // On génère le slug
    //         $slug = $slugger->slug($product->getName());
    //         $product->setSlug($slug);

    //         // On arrondit le prix 
    //         // $prix = $product->getPrice() * 100;
    //         // $product->setPrice($prix);

    //         // On stocke
    //         $em->persist($product);
    //         $em->flush();

    //         $this->addFlash('success', 'Produit modifié avec succès');

    //         // On redirige
    //         return $this->redirectToRoute('admin_products_index');
    //     }


    //     return $this->render('admin/products/edit.html.twig',[
    //         'promotionForm' => $promotionForm->createView(),
    //         'product' => $product
    //     ]);

    //     // return $this->renderForm('admin/products/edit.html.twig', compact('promotionForm'));
    //     // ['promotionForm' => $promotionForm]
    // }

    // #[Route('/suppression/{id}', name: 'delete')]
    // public function delete(Products $product, EntityManagerInterface $em): Response
    // {
    //     // On vérifie si l'utilisateur peut supprimer avec le Voter
    //     $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

    //     // Supprimer le produit de la base de données
    //     $em->remove($product);
    //     $em->flush();

    //     $this->addFlash('success', 'Produit supprimé avec succès');

    //     // Rediriger vers la liste des produits ou une autre page appropriée
    //     return $this->redirectToRoute('admin_products_index');
    // }
}