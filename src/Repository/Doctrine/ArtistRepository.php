<?php

namespace App\Repository\Doctrine;

use App\Entity\Artist;
use App\Repository\ArtistRepository as ArtistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class ArtistRepository implements ArtistRepositoryInterface
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
        $this->repository = $entityManager->getRepository(Artist::class);
    }

    /**
     * @param Artist $artist
     */
    public function save(Artist $artist): void
    {
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
    }

    /**
     * @param Artist $artist
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
