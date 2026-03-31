<?php

namespace App\Infrastructure\Controller\Event;

use App\Application\Command\DeleteEventCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteEventController extends AbstractController
{
    #[Route('/api/events/{id}', name: 'api_event_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id, MessageBusInterface $bus): JsonResponse
    {
        $command = new DeleteEventCommand($id);

        try {
            $bus->dispatch($command);
            return new JsonResponse(['status' => 'Event deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
