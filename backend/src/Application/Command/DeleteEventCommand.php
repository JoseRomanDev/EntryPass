<?php

namespace App\Application\Command;

readonly class DeleteEventCommand
{
    public function __construct(
        public string $id
    ) {}
}
