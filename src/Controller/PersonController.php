<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\GameRepository;
use App\Repository\PersonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends Controller
{
    private const MAX_PAGE_SIZE = 1000;
    private const DEFAULT_PAGE_SIZE = 500;

    /** @var PersonRepository */
    private $personRepository;

    /** @var GameRepository */
    private $gameRepository;

    public function __construct(
        PersonRepository $personRepository,
        GameRepository $gameRepository
    ) {
        $this->personRepository = $personRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route(
     *     "/persons/{page}/{pageSize}",
     *     defaults={"page"=1, "pageSize"=null},
     *     requirements={"page"="\d+", "pageSize"="\d+"},
     *     name="persons"
     * )
     *
     * @param int|null $page
     *
     * @return Response
     */
    public function index(?int $page = 1, ?int $pageSize)
    {
        $pageSize = $pageSize !== null ? $pageSize : $this::DEFAULT_PAGE_SIZE;

        if ($pageSize > $this::MAX_PAGE_SIZE) {
            return $this->redirectToRoute(
                'persons',
                [
                    'page' => $page,
                    'pageSize' => $this::MAX_PAGE_SIZE,
                ],
                Response::HTTP_PERMANENTLY_REDIRECT
            );
        }

        $offset = ($page - 1) * $pageSize;

        $records = $this->personRepository->count();
        $persons = $this->personRepository->list(
            $pageSize,
            $offset
        );

        return new JsonResponse([
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $records,
            'persons' => $persons,
        ]);
    }

    /**
     * @Route(
     *     "/persons/{person}",
     *     requirements={"person"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}"},
     *     name="person"
     * )
     *
     * @param Person $person
     *
     * @return JsonResponse
     */
    public function show(Person $person)
    {
        return new JsonResponse(
            $person
        );
    }

    /**
     * @Route(
     *     "/persons/{person}/games",
     *     requirements={"person"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}"},
     *     name="person_games"
     * )
     *
     * @param Person $person
     *
     * @return JsonResponse
     */
    public function games(Person $person)
    {
        return JsonResponse::create([
            'asDesigner' => $this->gameRepository->findByDesigner($person),
            'asArtist' => $this->gameRepository->findByArtist($person),
        ]);
    }
}
