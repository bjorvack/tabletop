<?php

namespace App\Repository\Doctrine;

use App\Entity\Publisher;
use App\Repository\PublisherRepository as PublisherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class PublisherRepository implements PublisherRepositoryInterface
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
        $this->repository = $entityManager->getRepository(Publisher::class);
    }

    /**
     * @param Publisher $publisher
     */
    public function save(Publisher $publisher): void
    {
        $this->entityManager->persist($publisher);
        $this->entityManager->flush();
    }

    /**
     * @param Publisher $publisher
     */
    public function remove(Publisher $publisher): void
    {
        $this->entityManager->remove($publisher);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return Publisher|null
     */
    public function find(UuidInterface $uuid): ?Publisher
    {
        return $this->repository->find($uuid);
    }
}
