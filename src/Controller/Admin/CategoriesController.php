<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $categorie = new Categories();

        // On crée le formulaire
        $categorieForm = $this->createForm(CategoriesFormType::class, $categorie);

        // On traite la requête du formulaire
        $categorieForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($categorieForm->isSubmitted() && $categorieForm->isValid()){
            // On récupère les images

            // On génère le slug
            $slug = $slugger->slug($categorie->getName());
            $categorie->setSlug($slug);

            // Récupérer la valeur du champ parent_id
            $parentId = $categorieForm->get('parent_id')->getData();

            // Définir la valeur de parent_id pour l'entité Categories
            $categorie->setParent($parentId);

            // On arrondit le prix 
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // On stocke
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig',[
            'categorieForm' => $categorieForm->createView()
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Categories $categorie, EntityManagerInterface $em): Response
    {
        // On vérifie si l'utilisateur peut supprimer avec le Voter
        $this->denyAccessUnlessGranted('ROLE_ADMIN', $categorie);

        // Supprimer le produit de la base de données
        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimé avec succès');

        // Rediriger vers la liste des produits ou une autre page appropriée
        return $this->redirectToRoute('admin_categories_index');
    }
}