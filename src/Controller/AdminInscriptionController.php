<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\InscriptionRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/inscriptions')]
class AdminInscriptionController extends AbstractController
{
    public function __construct(
        private InscriptionRepository $inscriptionRepository,
        private EvenementRepository $evenementRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'admin_inscriptions_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $eventId = $request->query->get('event');
        $inscriptions = [];
        $selectedEvent = null;

        if ($eventId) {
            $selectedEvent = $this->evenementRepository->find($eventId);
            if ($selectedEvent) {
                $inscriptions = $this->inscriptionRepository->findBy(
                    ['event_id' => $selectedEvent],
                    ['createdAt' => 'DESC']
                );
            }
        } else {
            $inscriptions = $this->inscriptionRepository->findBy(
                [],
                ['createdAt' => 'DESC']
            );
        }

        $evenements = $this->evenementRepository->findAll();
        
        // Calculer le total des places réservées pour chaque événement
        $totalPlaces = 0;
        foreach ($inscriptions as $inscription) {
            $totalPlaces += $inscription->getPlacesNumber();
        }

        return $this->render('admin/inscription/index.html.twig', [
            'inscriptions' => $inscriptions,
            'evenements' => $evenements,
            'selectedEvent' => $selectedEvent,
            'totalPlaces' => $totalPlaces,
        ]);
    }

    #[Route('/{id}', name: 'admin_inscriptions_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('admin/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{id}', name: 'admin_inscriptions_delete', methods: ['POST'])]
    public function delete(Request $request, Inscription $inscription): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inscription->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($inscription);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'inscription a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_inscriptions_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('inscription/{id}', name: 'admin_inscriptions_event')]
    public function createInscription(EntityManagerInterface $entityManager, Request $request, int $id, EvenementRepository $evenementRepository): Response
    {
        $inscription = new Inscription();
        $event = $this->evenementRepository->find($id);
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inscription = $form->getData();
            $inscription->setCreatedAt(new \DateTimeImmutable());
            $inscription->setEventId($event);
            $entityManager->persist($inscription);
            $entityManager->flush();
            return $this->redirectToRoute('admin_inscriptions_index', [], Response::HTTP_SEE_OTHER);        
            }

        return $this->render('admin/inscription/new.html.twig', [
            'form' => $form,
        ]);
       
    }
}
