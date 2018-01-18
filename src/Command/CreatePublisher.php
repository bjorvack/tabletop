<?php

namespace App\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreatePublisher
{
    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $website;

    /** @var int|null */
    private $boardGameGeekId;

    /**
     * @param string      $name
     * @param null|string $description
     * @param string|null $website
     * @param int|null    $boardGameGeekId
     */
    public function __construct(
        string $name,
        ?string $description,
        ?string $website,
        ?int $boardGameGeekId = null
    ) {
        $this->uuid = Uuid::uuid4();
        $this->name = $name;
        $this->description = $description;
        $this->website = $website;
        $this->boardGameGeekId = $boardGameGeekId;
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
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @return int|null
     */
    public function getBoardGameGeekId(): ?int
    {
        return $this->boardGameGeekId;
    }
}
