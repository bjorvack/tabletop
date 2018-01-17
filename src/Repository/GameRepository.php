<?php

namespace App\Repository;

use App\Entity\Game;
use Ramsey\Uuid\UuidInterface;

interface GameRepository
{
    public function save(Game $game): void;

    public function remove(Game $game): void;

    public function find(UuidInterface $uuid): ?Game;
}
