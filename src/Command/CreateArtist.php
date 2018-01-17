<?php

namespace App\Command;

use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateArtist
{
    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $name;

    /** @var string|null */
    private $website;

    /** @var Collection|null */
    private $games;

    /**
     * @param string          $name
     * @param string|null     $website
     * @param Collection|null $games
     */
    public function __construct(string $name, ?string $website, ?Collection $games)
    {
        $this->uuid = Uuid::uuid4();
        $this->name = $name;
        $this->games = $games;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @return Collection|null
     */
    public function getGames(): ?Collection
    {
        return $this->games;
    }
}
