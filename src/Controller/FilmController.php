<?php

namespace App\Controller;

use App\Service\FilmService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/posts', name: 'api_posts_')]
class FilmController extends AbstractController
{
    public function __construct(
        private readonly FilmService         $filmService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
        private readonly Security            $security,
    )
    {
    }

    #[Route("/test", name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(['test' => 'test'], Response::HTTP_OK);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(50, max(1, (int)$request->query->get('limit', 10)));

        $result = $this->filmService->getPaginatedPosts($page, $limit);

        return $this->json($result, Response::HTTP_OK, [], [
            'groups' => ['post:list'],
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $post = $this->filmService->getFilm($id);

        return $this->json($post, Response::HTTP_OK, [], [
            'groups' => ['post:read'],
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
//        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        $post = $this->filmService->createPost($data);

        return $this->json($post, Response::HTTP_CREATED, [], [
            'groups' => ['post:read'],
        ]);
    }
}
