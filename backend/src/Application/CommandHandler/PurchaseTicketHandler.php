<?php

namespace App\Application\CommandHandler;

use App\Application\Command\PurchaseTicketCommand;
use App\Application\Message\SendPurchaseEmailMessage;
use App\Domain\Entity\Purchase;
use App\Domain\Entity\Ticket;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Repository\PurchaseRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Port\PaymentGatewayInterface;

#[AsMessageHandler]
class PurchaseTicketHandler
{
    public function __construct(
        private EventRepositoryInterface    $eventRepository,
        private UserRepositoryInterface     $userRepository,
        private PurchaseRepositoryInterface $purchaseRepository,
        private MessageBusInterface         $bus,
        private PaymentGatewayInterface     $paymentGateway
    ) {}

    public function __invoke(PurchaseTicketCommand $command): Purchase
    {
        // 1. Recuperar entidades
        $user  = $this->userRepository->findByEmail($command->getUserEmail());
        $event = $this->eventRepository->findById($command->getEventId());

        if (!$user || !$event) {
            throw new \Exception("Usuario o Evento no encontrado en la base de datos.");
        }

        // 2. Comprobar capacidad
        $quantity = $command->getQuantity();
        if ($event->getCapacity() < $quantity) {
            throw new \Exception("No hay suficientes entradas disponibles.");
        }

        // 3. Comprobar límite de 4 entradas por usuario para este evento
        $purchasedAlready = $this->purchaseRepository->countTicketsByUserAndEvent($user, $event);
        if (($purchasedAlready + $quantity) > 4) {
            throw new \Exception(sprintf(
                "Límite excedido. Ya posees %d entradas y el máximo permitido por usuario es de 4 para este evento.",
                $purchasedAlready
            ));
        }

        // 4. Procesar el pago simulado
        $totalPrice = $event->getPrice() * $quantity;
        $this->paymentGateway->processPayment($totalPrice);

        // 5. Crear la Purchase
        $purchase = new Purchase(
            id:         $this->generateUuid(),
            user:       $user,
            event:      $event,
            quantity:   $quantity,
            totalPrice: $totalPrice
        );

        // 6. Crear un Ticket por cada unidad
        for ($i = 0; $i < $quantity; $i++) {
            $ticket = new Ticket(
                id:          $this->generateUuid(),
                purchase:    $purchase,
                qrCodeHash:  $this->generateQrHash($purchase->getId(), $i)
            );
            $purchase->addTicket($ticket);
        }

        // 7. Reducir capacidad del evento
        $event->setCapacity($event->getCapacity() - $quantity);
        $this->eventRepository->save($event);

        // 8. Persistir Purchase (cascade persist guarda los Tickets automáticamente)
        $this->purchaseRepository->save($purchase);

        // 9. Despachar mensaje al Worker (RabbitMQ) → generará el QR y enviará el email
        $this->bus->dispatch(new SendPurchaseEmailMessage($purchase->getId()));

        return $purchase;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function generateQrHash(string $purchaseId, int $index): string
    {
        return hash('sha256', $purchaseId . '_ticket_' . $index . '_' . microtime(true));
    }
}