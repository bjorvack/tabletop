<?php

namespace App\Repository;

use App\Entity\Person;
use Ramsey\Uuid\UuidInterface;

interface PersonRepository
{
    public function save(Person $person): void;

    public function remove(Person $person): void;

    public function find(UuidInterface $uuid): ?Person;

    public function findByBoardGameGeekId(int $id): ?Person;
}
