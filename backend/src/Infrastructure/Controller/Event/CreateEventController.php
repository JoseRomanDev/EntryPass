<?php

namespace App\Infrastructure\Controller\Event;

use App\Application\Command\CreateEventCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;


class CreateEventController extends AbstractController
{
    #[Route('/api/events', name: 'api_event_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, MessageBusInterface $bus): JsonResponse
    {
        // 1. Extraemos los datos del JSON
        $data = json_decode($request->getContent(), true);

        // 2. Validación básica
        if (!$data || !isset($data['title'], $data['date'], $data['price'], $data['capacity'])) {
            return new JsonResponse(['error' => 'Missing required fields (title, date, price, capacity)'], Response::HTTP_BAD_REQUEST);
        }

        // 3. Creamos el Command (La Intención)
        $command = new CreateEventCommand(
            Uuid::v4()->toRfc4122(),
            (string) $data['title'],
            (string) ($data['description'] ?? ''),
            (string) $data['date'],
            (float) $data['price'],
            (int) $data['capacity'],
            (bool) ($data['status'] ?? true) // Default to true (active) if not provided
        );

        // 4. Lanzamos el Command al Bus
        // El Bus buscará automáticamente al 'CreateEventHandler' gracias al atributo #[AsMessageHandler]
        $bus->dispatch($command);

        return new JsonResponse([
            'status' => 'Event created',
            'id' => $command->id
        ], Response::HTTP_CREATED);
    }
}