<?php

namespace App\Controller;

use App\Entity\Publisher;
use App\Repository\GameRepository;
use App\Repository\PublisherRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PublisherController extends Controller
{
    private const MAX_PAGE_SIZE = 1000;
    private const DEFAULT_PAGE_SIZE = 500;

    /** @var PublisherRepository */
    private $publisherRepository;

    /** @var GameRepository */
    private $gameRepository;

    public function __construct(
        PublisherRepository $publisherRepository,
        GameRepository $gameRepository
    ) {
        $this->publisherRepository = $publisherRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route(
     *     "/publishers/{page}/{pageSize}",
     *     defaults={"page"=1, "pageSize"=null},
     *     requirements={"page"="\d+", "pageSize"="\d+"},
     *     name="publishers"
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

        $records = $this->publisherRepository->count();
        $publishers = $this->publisherRepository->list(
            $pageSize,
            $offset
        );

        return new JsonResponse([
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $records,
            'publishers' => $publishers,
        ]);
    }

    /**
     * @Route(
     *     "/publishers/{publisher}",
     *     requirements={"publisher"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}"},
     *     name="publisher"
     * )
     *
     * @param Publisher $publisher
     *
     * @return JsonResponse
     */
    public function show(Publisher $publisher)
    {
        return new JsonResponse(
            $publisher
        );
    }

    /**
     * @Route(
     *     "/publishers/{publisher}/games",
     *     requirements={"publisher"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}"},
     *     name="publisher_games"
     * )
     *
     * @param Publisher $publisher
     *
     * @return JsonResponse
     */
    public function games(Publisher $publisher)
    {
        return new JsonResponse(
            $this->gameRepository->findByPublisher($publisher)
        );
    }
}
