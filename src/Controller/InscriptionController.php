<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscription/{id}', name: 'app_inscription_event', methods: ['GET', 'POST'])]
    public function create(
        int $id,
        Request $request,
        EvenementRepository $evenementRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $event = $evenementRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inscription->setCreatedAt(new \DateTimeImmutable());
            $inscription->setEventId($event);
            
            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Votre inscription a été enregistrée avec succès !');
            
            return $this->redirectToRoute('app_evenement_show', ['id' => $id]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez vérifier les champs.');
        }

        return $this->render('admin/inscription/new.html.twig', [
            'form' => $form,
            'evenement' => $event,
        ]);
    }
}
