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
        if (!file_exists($createGame->getImage())) {
            $image = $this->downloadImage(
                $createGame->getImage(),
                (string) $createGame->getUuid()
            );
        }

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

    /**
     * @param string $image
     * @param string $destination
     *
     * @return string
     */
    private function downloadImage(string $image, string $destination): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $image);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $imageData = curl_exec($ch);
        curl_close($ch);

        $path = '/assets/images/games/'.$destination;

        $saveFile = fopen($this->kernel->getRootDir().'/../public'.$path, 'w');
        fwrite($saveFile, $imageData);
        fclose($saveFile);

        return $path;
    }
}
