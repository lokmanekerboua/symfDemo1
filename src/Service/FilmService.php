<?php

namespace App\Service;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FilmService
{
    public function __construct(
        private readonly FilmRepository         $filmRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function getPaginatedPosts(int $page, int $limit): array
    {
        return [
            'data'  => $this->filmRepository->findPaginated($page, $limit),
            'total' => $this->filmRepository->countAll(),
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function getFilm(int $id): Film
    {
        $post = $this->filmRepository->find($id);

        if (!$post) {
            throw new NotFoundHttpException("Post #$id introuvable");
        }

        return $post;
    }

    public function createPost(array $data): Film
    {
        $film = new Film();
        $film->setTitle($data['title']);
        $film->setBody($data['body']);
        $film->setAuthor($data['author']);
        $film->setPicture($data['picture']);

        $this->em->persist($film);
        $this->em->flush();

        return $film;
    }
}
