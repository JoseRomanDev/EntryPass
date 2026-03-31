<?php

namespace App\Application\Command;

readonly class UpdateEventCommand
{
    public function __construct(
        public string $id,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $date = null,
        public ?float $price = null,
        public ?int $capacity = null,
        public ?bool $status = null
    ) {}
}
