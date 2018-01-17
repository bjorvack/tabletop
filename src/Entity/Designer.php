<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Designer
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
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Game",
     *     mappedBy="designers"
     * )
     */
    private $games;

    /**
     * @param UuidInterface $uuid
     * @param string        $name
     * @param null|string   $website
     */
    public function __construct(
        UuidInterface $uuid,
        string $name,
        ?string $website,
        ?Collection $games
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->website = $website;
        $this->games = $games instanceof Collection ? $games : new ArrayCollection();
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
     * @return Collection
     */
    public function getGames(): Collection
    {
        return $this->games;
    }
}
