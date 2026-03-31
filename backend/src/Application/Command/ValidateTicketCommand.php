<?php

namespace App\Application\Command;

class ValidateTicketCommand
{
    public function __construct(private string $qrHash)
    {
    }

    public function getQrHash(): string
    {
        return $this->qrHash;
    }
}
