<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Publisher
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(
     *     type="string",
     *     nullable=true
     * )
     */
    private $website;

    /**
     * @var int|null
     *
     * @ORM\Column(
     *     type="integer",
     *     nullable=true,
     *     unique=true
     * )
     */
    private $boardGameGeekId;

    /**
     * @param UuidInterface $uuid
     * @param string $name
     * @param null|string $website
     * @param int|null $boardGameGeekId
     */
    public function __construct(
        UuidInterface $uuid,
        string $name,
        ?string $website,
        ?int $boardGameGeekId
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
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
