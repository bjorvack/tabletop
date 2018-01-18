<?php

namespace App\Command;

use App\Entity\Game;
use App\Repository\GameRepository;

class CreateGameHandler
{
    /** @var GameRepository */
    private $gameRepository;

    /**
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param CreateGame $createGame
     */
    public function handle(CreateGame $createGame): void
    {
        $this->gameRepository->save(
            new Game(
                $createGame->getUuid(),
                $createGame->getTitle(),
                $createGame->getDescription(),
                $createGame->getPublishedOn(),
                $createGame->getImage(),
                $createGame->getArtists(),
                $createGame->getDesigners(),
                $createGame->getPublishers(),
                $createGame->getBoardGameGeekId()
            )
        );
    }
}
