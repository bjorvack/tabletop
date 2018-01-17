<?php

namespace App\Repository\Doctrine;

use App\Entity\Game;
use App\Repository\GameRepository as GameRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\UuidInterface;

class GameRepository implements GameRepositoryInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var EntityRepository */
    private $repository;

    /**
     * @param EntityManager $entityManager
     * @param EntityRepository $repository
     */
    public function __construct(
        EntityManager $entityManager,
        EntityRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @param Game $game
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Game $game): void
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * @param Game $game
     *
     * @throws ORMException
     * @throws OptimisticLockException
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