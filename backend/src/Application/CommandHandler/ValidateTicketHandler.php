<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ValidateTicketCommand;
use App\Domain\Entity\Ticket;
use App\Domain\Repository\TicketRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ValidateTicketHandler
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {}

    public function __invoke(ValidateTicketCommand $command): Ticket
    {
        $ticket = $this->ticketRepository->findByQrHash($command->getQrHash());

        if (!$ticket) {
            throw new \Exception("Ticket no encontrado con este código QR.");
        }

        // El dominio se encarga de cambiar el estado y validar si ya estaba usado
        $ticket->validateEntry();

        // Persistimos el estado actualizado
        $this->ticketRepository->save($ticket);

        return $ticket;
    }
}
