<?php

namespace App\Command;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class CreateGameHandler
{
    /** @var GameRepository */
    private $gameRepository;

    /** @var KernelInterface */
    private $kernel;

    /**
     * @param GameRepository  $gameRepository
     * @param KernelInterface $kernel
     */
    public function __construct(GameRepository $gameRepository, KernelInterface $kernel)
    {
        $this->gameRepository = $gameRepository;
        $this->kernel = $kernel;
    }

    /**
     * @param CreateGame $createGame
     */
    public function handle(CreateGame $createGame): void
    {
        $image = $createGame->getImage();

        $this->gameRepository->save(
            new Game(
                $createGame->getUuid(),
                $createGame->getTitle(),
                $createGame->getDescription(),
                $createGame->getMinPlayers(),
                $createGame->getMaxPlayers(),
                $createGame->getPublishedOn(),
                $image,
                $createGame->getArtists(),
                $createGame->getDesigners(),
                $createGame->getPublishers(),
                $createGame->getBoardGameGeekId()
            )
        );
    }
}
