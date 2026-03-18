<?php

namespace App\Application\Command;

class PurchaseTicketCommand
{
    public function __construct(
        private string $eventId,
        private int $quantity,
        private string $userEmail // Usamos email porque es lo que tiene tu Token
    ) {}

    public function getEventId(): string { return $this->eventId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUserEmail(): string { return $this->userEmail; }
}