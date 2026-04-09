<?php

namespace App\Application\CommandHandler;

use App\Application\Command\PurchaseTicketCommand;
use App\Application\Message\SendPurchaseEmailMessage;
use App\Domain\Entity\Purchase;
use App\Domain\Entity\Ticket;
use App\Domain\Exception\InsufficientCapacityException;
use App\Domain\Exception\PurchaseLimitExceededException;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Repository\PurchaseRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Port\PaymentGatewayInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class PurchaseTicketHandler
{
    public function __construct(
        private EventRepositoryInterface    $eventRepository,
        private UserRepositoryInterface     $userRepository,
        private PurchaseRepositoryInterface $purchaseRepository,
        private MessageBusInterface         $bus,
        private PaymentGatewayInterface     $paymentGateway,
        private EntityManagerInterface      $entityManager
    ) {}

    public function __invoke(PurchaseTicketCommand $command): Purchase
    {
        return $this->entityManager->wrapInTransaction(function() use ($command) {
            // 1. Recuperar entidades
            $user  = $this->userRepository->findByEmail($command->getUserEmail());
            $event = $this->eventRepository->findById($command->getEventId());

            if (!$user || !$event) {
                throw new \RuntimeException("Usuario o Evento no encontrado en la base de datos.");
            }

            // 2. Comprobar capacidad
            $quantity = $command->getQuantity();
            if ($event->getCapacity() < $quantity) {
                throw new InsufficientCapacityException($quantity, $event->getCapacity());
            }

            // 3. Comprobar límite de 4 entradas por usuario para este evento
            $purchasedAlready = $this->purchaseRepository->countTicketsByUserAndEvent($user, $event);
            if (($purchasedAlready + $quantity) > 4) {
                throw new PurchaseLimitExceededException($purchasedAlready, 4);
            }

            // 4. Procesar el pago simulado
            $totalPrice = $event->getPrice() * $quantity;
            $this->paymentGateway->processPayment($totalPrice);

            // 5. Crear la Purchase
            $purchase = new Purchase(
                id:         Uuid::v4()->toRfc4122(),
                user:       $user,
                event:      $event,
                quantity:   $quantity,
                totalPrice: $totalPrice
            );

            // 6. Crear un Ticket por cada unidad
            for ($i = 0; $i < $quantity; $i++) {
                $ticket = new Ticket(
                    id:          Uuid::v4()->toRfc4122(),
                    purchase:    $purchase,
                    qrCodeHash:  $this->generateQrHash($purchase->getId(), $i)
                );
                $purchase->addTicket($ticket);
            }

            // 7. Reducir capacidad del evento
            $event->setCapacity($event->getCapacity() - $quantity);
            $this->eventRepository->save($event);

            // 8. Persistir Purchase
            $this->purchaseRepository->save($purchase);

            // 9. Despachar mensaje al Worker
            $this->bus->dispatch(new SendPurchaseEmailMessage($purchase->getId()));

            return $purchase;
        });
    }

    private function generateQrHash(string $purchaseId, int $index): string
    {
        return hash('sha256', $purchaseId . '_ticket_' . $index . '_' . bin2hex(random_bytes(8)));
    }
}