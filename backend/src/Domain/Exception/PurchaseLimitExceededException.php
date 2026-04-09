<?php

namespace App\Domain\Exception;

use DomainException;

class PurchaseLimitExceededException extends DomainException
{
    public function __construct(int $current, int $limit)
    {
        parent::__construct(sprintf("Límite excedido. Ya posees %d entradas y el máximo permitido es de %d para este evento.", $current, $limit));
    }
}
