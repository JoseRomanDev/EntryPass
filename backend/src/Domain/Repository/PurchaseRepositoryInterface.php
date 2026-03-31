<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Purchase;
use App\Domain\Entity\User;
use App\Domain\Entity\Event;

interface PurchaseRepositoryInterface
{
    public function save(Purchase $purchase): void;
    public function findById(string $id): ?Purchase;

    /** @return Purchase[] */
    public function findByUser(User $user): array;

    public function countTicketsByUserAndEvent(User $user, Event $event): int;
}
