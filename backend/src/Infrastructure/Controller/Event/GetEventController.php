<?php

namespace App\Infrastructure\Controller\Event;

use App\Domain\Repository\EventRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetEventController extends AbstractController
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {}

    #[Route('/api/events/{id}', name: 'api_event_get', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $event = $this->repository->findById($id);

        if (!$event) {
            return new JsonResponse(['error' => 'Evento no encontrado'], 404);
        }

        return new JsonResponse([
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'date' => $event->getDate()->format('Y-m-d H:i:s'),
            'price' => $event->getPrice(),
            'capacity' => $event->getCapacity(),
            'status' => $event->getStatus(),
        ], 200);
    }
}
