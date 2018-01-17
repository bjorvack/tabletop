<?php

namespace App\Repository\Doctrine;

use App\Entity\Designer;
use App\Repository\DesignerRepository as DesignerRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\UuidInterface;

class DesignerRepository implements DesignerRepositoryInterface
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
     * @param Designer $designer
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Designer $designer): void
    {
        $this->entityManager->persist($designer);
        $this->entityManager->flush();
    }

    /**
     * @param Designer $designer
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Designer $designer): void
    {
        $this->entityManager->remove($designer);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return Designer|null
     */
    public function find(UuidInterface $uuid): ?Designer
    {
        return $this->repository->find($uuid);
    }
}