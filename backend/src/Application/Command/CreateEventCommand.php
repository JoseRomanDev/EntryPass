<?php

namespace App\Application\Command;

readonly class CreateEventCommand
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $date, 
        public float $price,
        public int $capacity,
        public bool $status
    ) {}
}