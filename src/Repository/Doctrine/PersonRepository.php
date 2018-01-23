<?php

namespace App\Repository\Doctrine;

use App\Entity\Person;
use App\Repository\PersonRepository as PersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\UuidInterface;

class PersonRepository implements PersonRepositoryInterface
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
        $this->repository = $entityManager->getRepository(Person::class);
    }

    /**
     * @return int
     *
     * @throws NonUniqueResultException
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
     * @param Person $person
     */
    public function save(Person $person): void
    {
        $this->entityManager->persist($person);
        $this->entityManager->flush();
    }

    /**
     * @param Person $person
     */
    public function remove(Person $person): void
    {
        $this->entityManager->remove($person);
        $this->entityManager->flush();
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return Person|null
     */
    public function find(UuidInterface $uuid): ?Person
    {
        return $this->repository->find($uuid);
    }

    /**
     * @param int $id
     *
     * @return Person|null
     */
    public function findByBoardGameGeekId(int $id): ?Person
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
