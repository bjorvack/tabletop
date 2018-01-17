<?php

namespace App\Repository;

use App\Entity\Designer;
use Ramsey\Uuid\UuidInterface;

interface DesignerRepository
{
    public function save(Designer $designer): void;

    public function remove(Designer $designer): void;

    public function find(UuidInterface $uuid): ?Designer;
}
