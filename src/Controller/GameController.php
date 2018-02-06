<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    private const MAX_PAGE_SIZE = 1000;
    private const DEFAULT_PAGE_SIZE = 500;

    /** @var GameRepository */
    private $gameRepository;

    public function __construct(
        GameRepository $gameRepository
    ) {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route(
     *     "/games/{page}/{pageSize}",
     *     defaults={"page"=1, "pageSize"=null},
     *     requirements={"page"="\d+", "pageSize"="\d+"},
     *     name="games"
     * )
     *
     * @param int|null $page
     *
     * @return Response
     */
    public function index(?int $page = 1, ?int $pageSize)
    {
        $pageSize = null !== $pageSize ? $pageSize : $this::DEFAULT_PAGE_SIZE;

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

        $records = $this->gameRepository->count();
        $games = $this->gameRepository->list(
            $pageSize,
            $offset
        );

        return new JsonResponse([
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $records,
            'games' => $games,
        ]);
    }

    /**
     * @Route(
     *     "/games/{game}",
     *     requirements={"game"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}"},
     *     name="game"
     * )
     *
     * @param Game $game
     *
     * @return JsonResponse
     */
    public function show(Game $game)
    {
        return new JsonResponse(
            $game
        );
    }
}
