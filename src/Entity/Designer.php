<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DesignerRepository")
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
     * @param UuidInterface $uuid
     * @param string $name
     * @param null|string $website
     */
    public function __construct(
        UuidInterface $uuid,
        string $name,
        ?string $website
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->website = $website;
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
}
