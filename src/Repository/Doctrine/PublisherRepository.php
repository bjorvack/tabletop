<?php

namespace App\Repository\Doctrine;

use App\Entity\Publisher;
use App\Repository\PublisherRepository as PublisherRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\UuidInterface;

class PublisherRepository implements PublisherRepositoryInterface
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
     * @param Publisher $publisher
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Publisher $publisher): void
    {
        $this->entityManager->persist($publisher);
        $this->entityManager->flush();
    }

    /**
     * @param Publisher $publisher
     *
     * @throws ORMException
     * @throws OptimisticLockException
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