<?php

namespace App\Repository\Doctrine;

use App\Entity\Game;
use App\Repository\GameRepository as GameRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class GameRepository implements GameRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EntityRepository */
    private $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Game::class);
    }

    /**
     * @param Game $game
     */
    public function save(Game $game): void
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * @param Game $game
     */
    public function remove(Game $game): void
    {
        $this->entityManager->remove($game);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return Game|null
     */
    public function find(UuidInterface $uuid): ?Game
    {
        return $this->repository->find($uuid);
    }
}