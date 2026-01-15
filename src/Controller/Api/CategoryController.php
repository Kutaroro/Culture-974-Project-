<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/category')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'api_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'color' => $category->getColor(),
                'icone' => $category->getIcone(),
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('', name: 'api_category_create', methods: ['PUT'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'error' => 'Données JSON invalides',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['name']) || empty($data['color']) || empty($data['icone'])) {
            return $this->json([
                'error' => 'Les champs "name", "color" et "icone" sont obligatoires',
            ], Response::HTTP_BAD_REQUEST);
        }

        $category = new Category();
        $category->setName((string) $data['name']);
        $category->setColor((string) $data['color']);
        $category->setIcone((string) $data['icone']);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'color' => $category->getColor(),
            'icone' => $category->getIcone(),
        ], Response::HTTP_CREATED);
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'api_category_update', methods: ['PATCH'])]
    public function update(int $id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'error' => 'Catégorie non trouvée',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'error' => 'Données JSON invalides',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('name', $data)) {
            $category->setName((string) $data['name']);
        }
        if (array_key_exists('color', $data)) {
            $category->setColor((string) $data['color']);
        }
        if (array_key_exists('icone', $data)) {
            $category->setIcone((string) $data['icone']);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'color' => $category->getColor(),
            'icone' => $category->getIcone(),
        ], Response::HTTP_OK);
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'api_category_delete', methods: ['DELETE'])]
    public function delete(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'error' => 'Catégorie non trouvée',
            ], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
