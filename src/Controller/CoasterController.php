<?php

namespace App\Controller;

use App\Entity\Coaster;
use App\Form\CoasterType;
use App\Repository\CategoryRepository;
use App\Repository\CoasterRepository;
use App\Repository\ParkRepository;
use App\Security\Voter\CoasterVoter;
use App\Service\FileUploader\FileUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CoasterController extends AbstractController
{
    #[Route(path: '/coaster')]
    public function index(
        CoasterRepository $coasterRepository,
        ParkRepository $parkRepository,
        CategoryRepository $categoryRepository,
        Request $request,
    ): Response
    {
        // Récupére toutes les entités Coaster
        // $entities = $coasterRepository->findAll();
        $parks = $parkRepository->findAll();
        $categories = $categoryRepository->findAll();

        // Valeurs envoyées depuis le formulaire de filtre
        $parkId = (int) $request->query->get('park');
        $categoryId = (int) $request->query->get('category');

        $count = 30;
        $page = (int) $request->query->get('p', 1); // 1 par défaut

        $entities = $coasterRepository->findFiltered($parkId, $categoryId, $count, $page);

        $pageCount = max(ceil($entities->count() / $count), 1);

        return $this->render('coaster/index.html.twig', [
            'entities' => $entities, // Envoi des entités dans la vue
            'parks' => $parks,
            'categories' => $categories,
            'parkId' => $parkId,
            'categoryId' => $categoryId,
            'pageCount' => $pageCount, // Nombre de pages
            'page' => $page, // Numéro de la page à afficher
        ]);
    }

    #[Route(path: '/coaster/add')]
    #[IsGranted('ROLE_USER')]
    public function add(EntityManagerInterface $em, Request $request, FileUploaderInterface $fileUploader): Response
    {
        $coaster = new Coaster();
        $coaster->setAuthor($this->getUser());

        $form = $this->createForm(CoasterType::class, $coaster);
        // Injecter les données $_POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $imageData = $form->get('image')->getData();
                if ($imageData) {
                    $filePath = $fileUploader->upload($imageData);
                    $coaster->setImageFileName($filePath);
                }

                $em->persist($coaster);
                $em->flush();

                $this->addFlash('success', 'Le coaster a été créé avec succès.');

                // redirection
                return $this->redirectToRoute('app_coaster_index');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du coaster.');
            }
        }

        return $this->render('/coaster/add.html.twig', [
            'coasterForm' => $form, // Envoi du formulaire dans la vue
        ]);
    }

    // {id<\d+>} est un paramètre de type entier de 1 ou plusieurs chiffres
    // Symfony utilise le param converter pour trouver l'entité Coaster depuis l'id 
    #[Route(path: '/coaster/{id<\d+>}/edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Coaster $entity,
        EntityManagerInterface $em,
        Request $request,
        FileUploaderInterface $fileUploader
    ): Response
    {
        $this->denyAccessUnlessGranted(CoasterVoter::EDIT, $entity, 'Vous n\'avez pas le droit de modifier ce coaster.');

        // dump($entity);
        $form = $this->createForm(CoasterType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // $form->get('image') Récupére le champ 'image'
                // ->getData() Retourne la donnée UploadedFile
                $imageData = $form->get('image')->getData();
                if ($imageData) {
                    if ($entity->getImageFileName()) {
                        $fileUploader->remove($entity->getImageFileName());
                    }

                    $filePath = $fileUploader->upload($imageData);
                    $entity->setImageFileName($filePath);
                }

                $em->flush();
                $this->addFlash('success', 'Le coaster a été mis à jour avec succès.');

                return $this->redirectToRoute('app_coaster_index');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du coaster.');
            }
        }

        return $this->render('/coaster/edit.html.twig', [
            'coasterForm' => $form,
        ]);
    }

    #[Route(path: '/coaster/{id<\d+>}/delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Coaster $entity, EntityManagerInterface $em, Request $request, FileUploaderInterface $fileUploader): Response
    {
        $this->denyAccessUnlessGranted(CoasterVoter::EDIT, $entity, 'Vous n\'avez pas le droit de supprimer ce coaster.');
        // $_POST['_token'] => $request->request 
        // $_GET['value'] => $request->query 
        // $_SERVER[] => $request->server 
        // $request->get('_token') => Deprecated

        if ($this->isCsrfTokenValid(
            'delete' . $entity->getId(), 
            $request->request->get('_token')
        )) {
            try {
                if ($entity->getImageFileName()) {
                    $fileUploader->remove($entity->getImageFileName());
                }

                $em->remove($entity);
                $em->flush();
                $this->addFlash('success', 'Le coaster a été supprimé avec succès.');

                // symfony console debug:router
                return $this->redirectToRoute('app_coaster_index');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du coaster.');
                return $this->redirectToRoute('app_coaster_index');
            }
        }

        $this->addFlash('error', 'Le jeton CSRF est invalide. Veuillez réessayer.');

        return $this->render('/coaster/delete.html.twig', [
            'coaster' => $entity,
        ]);
    }

    #[Route('/coaster/{id<\d+>}', methods: ['GET'])]
    public function show(Coaster $entity): Response
    {
        $this->denyAccessUnlessGranted(CoasterVoter::VIEW, $entity, 'Vous n\'avez pas le droit de consulter ce coaster.');

        return $this->render('/coaster/show.html.twig', [
            'entity' => $entity,
        ]);
    }
}
