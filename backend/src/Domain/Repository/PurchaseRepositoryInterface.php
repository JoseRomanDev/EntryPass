<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Purchase;
use App\Domain\Entity\User;

interface PurchaseRepositoryInterface
{
    public function save(Purchase $purchase): void;
    public function findById(string $id): ?Purchase;

    /** @return Purchase[] */
    public function findByUser(User $user): array;
}
