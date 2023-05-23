<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Entity\Promotions;
use App\Form\PromotionsFormType;
use App\Form\ProductsFormType;
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
use Symfony\Bridge\Doctrine\ManagerRegistry;

#[Route('/admin/promotion', name: 'admin_promotion_')]
class PromotionsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository, ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $promotions = $entityManager->getRepository(Promotions::class)->findAll();
        $produits = $productsRepository->findAll();
        return $this->render('admin/products/index.html.twig', compact('produits', 'promotions'));
    }

    #[Route('/ajout/{id}', name: 'ajout')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService, Products $product): Response
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
            
            $promotion->setProduct($product);

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

        return $this->renderForm('admin/products/promotions/add.html.twig', compact('promotionForm'));
        // ['promotionForm' => $promotionForm]
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Promotions $promotion, EntityManagerInterface $em): Response
    {
        // On vérifie si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $promotion);

        // Supprimer le produit de la base de données
        $em->remove($promotion);
        $em->flush();

        $this->addFlash('success', 'Promotion supprimée avec succès');

        // Rediriger vers la liste des produits ou une autre page appropriée
        return $this->redirectToRoute('admin_categories_index');
    }
}