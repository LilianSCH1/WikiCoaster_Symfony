<?php

namespace App\Controller;

use App\Entity\Park;
use App\Form\ParkType;
use App\Repository\ParkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/park')]
final class ParkController extends AbstractController
{
    #[Route(name: 'app_park_index', methods: ['GET'])]
    public function index(ParkRepository $parkRepository): Response
    {
        return $this->render('park/index.html.twig', [
            'parks' => $parkRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_park_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $park = new Park();
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($park);
                $entityManager->flush();
                $this->addFlash('success', 'Le parc a été créé avec succès.');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du parc.');
                return $this->render('park/new.html.twig', [
                    'park' => $park,
                    'form' => $form,
                ]);
            }

            return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('park/new.html.twig', [
            'park' => $park,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_park_show', methods: ['GET'])]
    public function show(Park $park): Response
    {
        return $this->render('park/show.html.twig', [
            'park' => $park,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_park_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Park $park, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParkType::class, $park);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Le parc a été mis à jour avec succès.');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du parc.');
                return $this->render('park/edit.html.twig', [
                    'park' => $park,
                    'form' => $form,
                ]);
            }

            return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('park/edit.html.twig', [
            'park' => $park,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_park_delete', methods: ['POST'])]
    public function delete(Request $request, Park $park, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$park->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($park);
                $entityManager->flush();
                $this->addFlash('success', 'Le parc a été supprimé avec succès.');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du parc.');
            }
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide. Veuillez réessayer.');
        }

        return $this->redirectToRoute('app_park_index', [], Response::HTTP_SEE_OTHER);
    }
}
