<?php

namespace App\Repository\Doctrine;

use App\Entity\Game;
use App\Entity\Person;
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

    /**
     * @param int $id
     *
     * @return Person|null
     */
    public function findByBoardGameGeekId(int $id): ?Game
    {
        return $this->repository->findOneBy(['boardGameGeekId' => $id]);
    }

    /**
     * @param Person $person
     *
     * @return array
     */
    public function findByDesigner(Person $person): array
    {
        return $this->repository->createQueryBuilder('g')
            ->innerJoin('g.designers', 'p')
            ->where('p.uuid = :designer')
            ->orderBy('g.title', 'DESC')
            ->setParameter('designer', (string) $person->getUuid())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Person $person
     *
     * @return array
     */
    public function findByArtist(Person $person): array
    {
        return $this->repository->createQueryBuilder('g')
            ->innerJoin('g.artists', 'p')
            ->where('p.uuid = :artist')
            ->orderBy('g.title', 'DESC')
            ->setParameter('artist', (string) $person->getUuid())
            ->getQuery()
            ->getResult();
    }
}
