<?php

namespace App\Infrastructure\Controller\Event;

use App\Domain\Repository\EventRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ListEventsController extends AbstractController
{
    public function __construct(
        private EventRepositoryInterface $repository,
        private \Symfony\Bundle\SecurityBundle\Security $security
    ) {}

    #[Route('/api/events', name: 'api_event_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        // Si es Admin, ve todos. Si no, solo los activos.
        $events = $this->security->isGranted('ROLE_ADMIN') 
            ? $this->repository->findAll() 
            : $this->repository->findActive();

        $data = array_map(function ($event) {
            return [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'date' => $event->getDate()->format('Y-m-d H:i:s'),
                'price' => $event->getPrice(),
                'capacity' => $event->getCapacity(),
                'status' => $event->getStatus(),
            ];
        }, $events);

        return new JsonResponse($data);
    }
}
