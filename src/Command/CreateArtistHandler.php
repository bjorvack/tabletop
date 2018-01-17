<?php

namespace App\Command;

use App\Entity\Artist;
use App\Repository\ArtistRepository;

class CreateArtistHandler
{
    /** @var ArtistRepository */
    private $artistRepository;

    /**
     * @param ArtistRepository $artistRepository
     */
    public function __construct(
        ArtistRepository $artistRepository
    ) {
        $this->artistRepository = $artistRepository;
    }

    /**
     * @param CreateArtist $createArtist
     */
    public function handle(CreateArtist $createArtist): void
    {
        $this->artistRepository->save(
            new Artist(
                $createArtist->getUuid(),
                $createArtist->getName(),
                $createArtist->getDescription(),
                $createArtist->getWebsite(),
                $createArtist->getGames()
            )
        );
    }
}
