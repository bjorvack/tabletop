<?php

namespace App\Repository;

use App\Entity\Publisher;
use Ramsey\Uuid\UuidInterface;

interface PublisherRepository
{
    public function count(): int;

    public function list(int $limit, int $offset): array;

    public function save(Publisher $publisher): void;

    public function remove(Publisher $publisher): void;

    public function find(UuidInterface $uuid): ?Publisher;

    public function findByBoardGameGeekId(int $id): ?Publisher;

    public function findByBoardGameGeekIds(array $ids): array;
}
