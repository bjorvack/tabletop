<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
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
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(
     *     type="text",
     *     nullable=true
     * )
     */
    private $description;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $publishedOn;

    /**
     * @param UuidInterface $uuid
     * @param string $title
     * @param null|string $description
     * @param DateTimeImmutable $publishedOn
     */
    public function __construct(
        UuidInterface $uuid,
        string $title,
        ?string $description,
        DateTimeImmutable $publishedOn
    ) {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->description = $description;
        $this->publishedOn = $publishedOn;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublishedOn(): DateTimeImmutable
    {
        return $this->publishedOn;
    }
}
