<?php

namespace App\Infrastructure\Controller\Ticket;

use App\Application\Command\PurchaseTicketCommand;
use App\Domain\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseTicketController extends AbstractController
{
    #[Route('/api/purchases', name: 'api_purchase_ticket', methods: ['POST'])]
    public function purchase(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['eventId'])) {
            return new JsonResponse(['error' => 'Falta el eventId'], 400);
        }

        $userEmail = $this->getUser()->getUserIdentifier();

        $command = new PurchaseTicketCommand(
            $data['eventId'],
            (int) ($data['quantity'] ?? 1),
            $userEmail
        );

        try {
            $envelope = $bus->dispatch($command);

            /** @var \App\Domain\Entity\Purchase $purchase */
            $purchase = $envelope->last(HandledStamp::class)?->getResult();

            $ticketIds = array_map(
                fn(Ticket $t) => $t->getId(),
                $purchase->getTickets()->toArray()
            );

            return new JsonResponse([
                'status'     => 'Compra procesada correctamente',
                'purchaseId' => $purchase->getId(),
                'ticketIds'  => $ticketIds,
                'quantity'   => $purchase->getQuantity(),
                'totalPrice' => $purchase->getTotalPrice(),
            ], 201);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}