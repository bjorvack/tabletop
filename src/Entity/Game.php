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
     * @var int
     *
     * @ORM\Column(
     *     type="integer",
     *     options={"default": 1}
     * )
     */
    private $minPlayers;

    /**
     * @var int|null
     *
     * @ORM\Column(
     *     type="integer",
     *     nullable=true
     * )
     */
    private $maxPlayers;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $publishedOn;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Person")
     * @ORM\JoinTable(
     *     name="game_artist",
     *     joinColumns={@ORM\JoinColumn(name="game_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="person_uuid", referencedColumnName="uuid")}
     * )
     */
    private $artists;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Person")
     * @ORM\JoinTable(
     *     name="game_designer",
     *     joinColumns={@ORM\JoinColumn(name="game_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="person_uuid", referencedColumnName="uuid")}
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
     * @ORM\JoinTable(
     *     name="game_publisher",
     *     joinColumns={@ORM\JoinColumn(name="game_uuid", referencedColumnName="uuid")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="publisher_uuid", referencedColumnName="uuid")}
     * )
     */
    private $publishers;

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
     * @param UuidInterface     $uuid
     * @param string            $title
     * @param null|string       $description
     * @param int               $minPlayers
     * @param int|null          $maxPlayers
     * @param DateTimeImmutable $publishedOn
     * @param string            $image
     * @param Collection|null   $artists
     * @param Collection|null   $designers
     * @param Collection|null   $publishers
     * @param int|null          $boardGameGeekId
     */
    public function __construct(
        UuidInterface $uuid,
        string $title,
        ?string $description,
        int $minPlayers,
        ?int $maxPlayers,
        DateTimeImmutable $publishedOn,
        string $image,
        ?Collection $artists,
        ?Collection $designers,
        ?Collection $publishers,
        ?int $boardGameGeekId
    ) {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->description = $description;
        $this->publishedOn = $publishedOn;
        $this->image = $image;
        $this->artists = $artists instanceof Collection ? $artists : new ArrayCollection();
        $this->designers = $designers instanceof Collection ? $designers : new ArrayCollection();
        $this->publishers = $publishers instanceof Collection ? $publishers : new ArrayCollection();
        $this->boardGameGeekId = $boardGameGeekId;
        $this->minPlayers = $minPlayers;
        $this->maxPlayers = $maxPlayers;
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
     * @return int
     */
    public function getMinPlayers(): int
    {
        return $this->minPlayers;
    }

    /**
     * @return int|null
     */
    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPublishedOn(): DateTimeImmutable
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
