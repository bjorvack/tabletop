<?php

namespace App\Repository\Doctrine;

use App\Entity\Designer;
use App\Repository\DesignerRepository as DesignerRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

class DesignerRepository implements DesignerRepositoryInterface
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
        $this->repository = $entityManager->getRepository(Designer::class);
    }

    /**
     * @param Designer $designer
     */
    public function save(Designer $designer): void
    {
        $this->entityManager->persist($designer);
        $this->entityManager->flush();
    }

    /**
     * @param Designer $designer
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

    /**
     * @param int $id
     *
     * @return Designer|null
     */
    public function findByBoardGameGeekId(int $id): ?Designer
    {
        return $this->repository->findOneBy(['boardGameGeekId' => $id]);
    }
}
