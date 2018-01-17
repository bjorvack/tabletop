<?php

namespace App\Command;

use App\Entity\Designer;
use App\Repository\DesignerRepository;

class CreateDesignerHandler
{
    /** @var DesignerRepository */
    private $designerRepository;

    /**
     * @param DesignerRepository $designerRepository
     */
    public function __construct(
        DesignerRepository $designerRepository
    ) {
        $this->designerRepository = $designerRepository;
    }

    /**
     * @param CreateDesigner $createDesigner
     */
    public function handle(CreateDesigner $createDesigner): void
    {
        $this->designerRepository->save(
            new Designer(
                $createDesigner->getUuid(),
                $createDesigner->getName(),
                $createDesigner->getDescription(),
                $createDesigner->getWebsite(),
                $createDesigner->getGames(),
                $createDesigner->getBoardGameGeekId()
            )
        );
    }
}
