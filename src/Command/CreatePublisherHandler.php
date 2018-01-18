<?php

namespace App\Command;

use App\Entity\Publisher;
use App\Repository\PublisherRepository;

class CreatePublisherHandler
{
    /** @var PublisherRepository */
    private $publisherRepository;

    /**
     * @param PublisherRepository $publisherRepository
     */
    public function __construct(
        PublisherRepository $publisherRepository
    ) {
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * @param CreatePublisher $createPublisher
     */
    public function handle(CreatePublisher $createPublisher): void
    {
        $this->publisherRepository->save(
            new Publisher(
                $createPublisher->getUuid(),
                $createPublisher->getName(),
                $createPublisher->getDescription(),
                $createPublisher->getWebsite(),
                $createPublisher->getBoardGameGeekId()
            )
        );
    }
}
