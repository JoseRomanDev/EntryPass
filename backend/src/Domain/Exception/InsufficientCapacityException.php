<?php

namespace App\Domain\Exception;

use DomainException;

class InsufficientCapacityException extends DomainException
{
    public function __construct(int $requested, int $available)
    {
        parent::__construct(sprintf("No hay suficientes entradas disponibles. Solicitadas: %d, Disponibles: %d", $requested, $available));
    }
}
