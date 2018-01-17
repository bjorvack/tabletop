<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Person;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

interface GameRepository
{
    public function save(Game $game): void;

    public function remove(Game $game): void;

    public function find(UuidInterface $uuid): ?Game;

    public function findByDesigner(Person $person): array;

    public function findByArtist(Person $person): array;
}
