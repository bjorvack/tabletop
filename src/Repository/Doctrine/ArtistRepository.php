<?php

namespace App\Repository\Doctrine;

use App\Entity\Artist;
use App\Repository\ArtistRepository as ArtistRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\UuidInterface;

class ArtistRepository implements ArtistRepositoryInterface
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
     * @param Artist $artist
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Artist $artist): void
    {
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
    }

    /**
     * @param Artist $artist
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Artist $artist): void
    {
        $this->entityManager->remove($artist);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return Artist|null
     */
    public function find(UuidInterface $uuid): ?Artist
    {
        return $this->repository->find($uuid);
    }
}