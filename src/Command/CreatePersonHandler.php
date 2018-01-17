<?php

namespace App\Command;

use App\Entity\Person;
use App\Repository\PersonRepository;

class CreatePersonHandler
{
    /** @var PersonRepository */
    private $personRepository;

    /**
     * @param PersonRepository $personRepository
     */
    public function __construct(
        PersonRepository $personRepository
    ) {
        $this->personRepository = $personRepository;
    }

    /**
     * @param CreatePerson $createPerson
     */
    public function handle(CreatePerson $createPerson): void
    {
        $this->personRepository->save(
            new Person(
                $createPerson->getUuid(),
                $createPerson->getName(),
                $createPerson->getDescription(),
                $createPerson->getWebsite(),
                $createPerson->getBoardGameGeekId()
            )
        );
    }
}
