<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Person;
use App\Entity\Publisher;
use Ramsey\Uuid\UuidInterface;

interface GameRepository
{
    public function count(): int;

    public function list(int $limit, int $offset): array;

    public function save(Game $game): void;

    public function remove(Game $game): void;

    public function find(UuidInterface $uuid): ?Game;

    public function findByBoardGameGeekId(int $id): ?Game;

    public function findByBoardGameGeekIds(array $ids): array;

    public function findByDesigner(Person $person): array;

    public function findByArtist(Person $person): array;

    public function findByPublisher(Publisher $publisher): array;
}
