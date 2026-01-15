<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\CategoryRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(Request $request, EvenementRepository $evenementRepository): Response
    {
        $qb = $evenementRepository->createQueryBuilder('e');
        $qb->where('e.date >= :today')
           ->setParameter('today', new \DateTime('today'))
           ->orderBy('e.date', 'ASC');
        
        // Filtrer par catégorie si le paramètre category est présent
        $categoryId = $request->query->getInt('category');
        if ($categoryId > 0) {
            $qb->andWhere('e.category_id = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
        
        $evenements = $qb->getQuery()->getResult();
        if (!$evenements) {
            return $this->redirectToRoute('app_erreur', ['erreur' => 'Evenements'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(int $id, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($id);
        if (!$evenement) {
            return $this->redirectToRoute('app_erreur', ['erreur' => 'Evenement'], Response::HTTP_SEE_OTHER);
        }
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/category/{id}', name: 'app_evenement_show_by_category', methods: ['GET'])]
    public function showByCategory(int $id, CategoryRepository $categoryRepository, EvenementRepository $evenementRepository): Response
    {
        $categorie = $categoryRepository->find($id);
        if (!$categorie) {
            return $this->redirectToRoute('app_erreur', ['erreur' => 'Catégorie'], Response::HTTP_SEE_OTHER);
        }
        $evenementsTrie = $evenementRepository->findBy(['category_id' => $categorie]);
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementsTrie,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
    {
        $evenement = $evenementRepository->find($id);
        if (!$evenement) {
            return $this->redirectToRoute('app_erreur', ['erreur' => 'Evenement'], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
    {
        $evenement = $evenementRepository->find($id);
        if (!$evenement) {
            return $this->redirectToRoute('app_erreur', ['erreur' => 'Evenement'], Response::HTTP_SEE_OTHER);
        }
        
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('erreur/{erreur}', name: 'app_erreur')]
    public function erreur(String $erreur): Response
    {
        return $this->render('bundles\TwigBundle\Exception\error404.html.twig', [
            'erreur' =>$erreur,
        ]);
    }
}
