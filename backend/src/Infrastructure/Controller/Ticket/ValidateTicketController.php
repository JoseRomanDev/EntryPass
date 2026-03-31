<?php

namespace App\Infrastructure\Controller\Ticket;

use App\Application\Command\ValidateTicketCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ValidateTicketController extends AbstractController
{
    #[Route('/api/tickets/validate', name: 'api_validate_ticket', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function validate(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['qrHash'])) {
            return new JsonResponse(['error' => 'Falta el parámetro qrHash'], 400);
        }

        $command = new ValidateTicketCommand($data['qrHash']);

        try {
            $envelope = $bus->dispatch($command);

            /** @var \App\Domain\Entity\Ticket $ticket */
            $ticket = $envelope->last(HandledStamp::class)?->getResult();

            return new JsonResponse([
                'status'    => 'Ticket validado correctamente',
                'ticketId'  => $ticket->getId(),
                'eventId'   => $ticket->getPurchase()->getEvent()->getId(),
                'userEmail' => $ticket->getPurchase()->getUser()->getEmail(),
                'scannedAt' => $ticket->getScannedAt()->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400); 
        }
    }
}
