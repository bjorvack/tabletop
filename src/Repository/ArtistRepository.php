<?php

namespace App\Repository;

use App\Entity\Artist;
use Ramsey\Uuid\UuidInterface;

interface ArtistRepository
{
    public function save(Artist $artist): void;

    public function remove(Artist $artist): void;

    public function find(UuidInterface $uuid): ?Artist;

    public function findByBoardGameGeekId(int $id): ?Artist;
}
