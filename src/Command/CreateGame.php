<?php

namespace App\Command;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateGame
{
    /** @var UuidInterface */
    private $uuid;

    /** @var string */
    private $title;

    /** @var string|null */
    private $description;

    /** @var DateTimeImmutable|null */
    private $publishedOn;

    /** @var string */
    private $image;

    /** @var Collection */
    private $artists;

    /** @var Collection */
    private $designers;

    /** @var Collection */
    private $publishers;

    /** @var int|null */
    private $boardGameGeekId;

    /**
     * @param string                 $title
     * @param null|string            $description
     * @param DateTimeImmutable|null $publishedOn
     * @param string                 $image
     * @param Collection|null        $artists
     * @param Collection|null        $designers
     * @param Collection|null        $publishers
     * @param int|null               $boardGameGeek
     */
    public function __construct(
        string $title,
        ?string $description,
        ?DateTimeImmutable $publishedOn,
        string $image,
        ?Collection $artists,
        ?Collection $designers,
        ?Collection $publishers,
        ?int $boardGameGeek
    ) {
        $this->uuid = Uuid::uuid4();
        $this->title = $title;
        $this->description = $description;
        $this->publishedOn = $publishedOn;
        $this->image = $image;
        $this->artists = $artists instanceof Collection ? $artists : new ArrayCollection();
        $this->designers = $designers instanceof Collection ? $designers : new ArrayCollection();
        $this->publishers = $publishers instanceof Collection ? $publishers : new ArrayCollection();
        $this->boardGameGeekId = $boardGameGeek;
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
     * @return DateTimeImmutable|null
     */
    public function getPublishedOn(): ?DateTimeImmutable
    {
        return $this->publishedOn;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @return Collection
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    /**
     * @return Collection
     */
    public function getDesigners(): Collection
    {
        return $this->designers;
    }

    /**
     * @return Collection
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    /**
     * @return int|null
     */
    public function getBoardGameGeekId(): ?int
    {
        return $this->boardGameGeekId;
    }
}
