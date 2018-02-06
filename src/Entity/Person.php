<?php

namespace App\Entity;

use App\Utils\StringUtils;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Person implements JsonSerializable
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
     * @param string        $name
     * @param string|null   $description
     * @param null|string   $website
     * @param int|null      $boardGameGeekId
     */
    public function __construct(
        UuidInterface $uuid,
        string $name,
        ?string $description,
        ?string $website,
        ?int $boardGameGeekId
    ) {
        $this->uuid = $uuid;
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
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'uuid' => (string) $this->getUuid(),
            'name' => StringUtils::cleanup($this->getName()),
            'description' => StringUtils::cleanup($this->getDescription()),
            'website' => $this->getWebsite(),
        ];
    }
}
