<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
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
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Artist",
     *     inversedBy="games"
     * )
     */
    private $artists;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Designer",
     *     inversedBy="games"
     * )
     */
    private $designers;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Publisher",
     *     inversedBy="games"
     * )
     */
    private $publishers;

    /**
     * @param UuidInterface $uuid
     * @param string $title
     * @param null|string $description
     * @param DateTimeImmutable $publishedOn
     * @param Collection|null $artists
     * @param Collection|null $designers
     * @param Collection|null $publishers
     */
    public function __construct(
        UuidInterface $uuid,
        string $title,
        ?string $description,
        DateTimeImmutable $publishedOn,
        ?Collection $artists,
        ?Collection $designers,
        ?Collection $publishers
    ) {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->description = $description;
        $this->publishedOn = $publishedOn;
        $this->artists = $artists instanceof Collection ? $artists : new ArrayCollection();
        $this->designers = $designers instanceof Collection ? $designers : new ArrayCollection();
        $this->publishers = $publishers instanceof Collection ? $publishers : new ArrayCollection();
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
}
