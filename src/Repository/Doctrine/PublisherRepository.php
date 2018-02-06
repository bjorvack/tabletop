<?php

namespace App\Repository\Doctrine;

use App\Entity\Publisher;
use App\Repository\PublisherRepository as PublisherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @throws NonUniqueResultException
     *
     * @return int
     */
    public function count(): int
    {
        return $this->repository->createQueryBuilder('p')
            ->select('count(p.uuid)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function list(int $limit, int $offset = 0): array
    {
        return $this->repository->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
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

    /**
     * @param int $id
     *
     * @return Publisher|null
     */
    public function findByBoardGameGeekId(int $id): ?Publisher
    {
        return $this->repository->findOneBy(['boardGameGeekId' => $id]);
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function findByBoardGameGeekIds(array $ids): array
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->where('p.boardGameGeekId in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
