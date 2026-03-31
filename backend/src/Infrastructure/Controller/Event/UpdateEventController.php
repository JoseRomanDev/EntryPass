<?php

namespace App\Infrastructure\Controller\Event;

use App\Application\Command\UpdateEventCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UpdateEventController extends AbstractController
{
    #[Route('/api/events/{id}', name: 'api_event_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id, Request $request, MessageBusInterface $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $command = new UpdateEventCommand(
            $id,
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['date'] ?? null,
            isset($data['price']) ? (float) $data['price'] : null,
            isset($data['capacity']) ? (int) $data['capacity'] : null,
            isset($data['status']) ? (bool) $data['status'] : null
        );

        try {
            $bus->dispatch($command);
            return new JsonResponse(['status' => 'Event updated'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
