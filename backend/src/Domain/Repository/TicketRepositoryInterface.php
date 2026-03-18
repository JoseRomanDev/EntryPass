<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Ticket;

interface TicketRepositoryInterface
{
    public function save(Ticket $ticket): void;
    public function findById(string $id): ?Ticket;
    public function findByQrHash(string $hash): ?Ticket;
}
