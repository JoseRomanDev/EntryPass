<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class Ticket
{
    private bool $isUsed;
    private ?DateTimeImmutable $scannedAt;

    public function __construct(
        private string   $id,
        private Purchase $purchase,
        private string   $qrCodeHash
    ) {
        $this->isUsed    = false;
        $this->scannedAt = null;
    }

    // ── Validación de entrada ────────────────────────────────────────────────

    public function validateEntry(): void
    {
        if ($this->isUsed) {
            throw new \Exception(
                "El ticket ya ha sido utilizado el "
                . $this->scannedAt->format('d-m-Y H:i:s')
            );
        }

        $this->isUsed    = true;
        $this->scannedAt = new DateTimeImmutable();
    }

    // ── Getters ─────────────────────────────────────────────────────────────

    public function getId(): string          { return $this->id; }
    public function getPurchase(): Purchase  { return $this->purchase; }
    public function isUsed(): bool           { return $this->isUsed; }
    public function getScannedAt(): ?DateTimeImmutable { return $this->scannedAt; }
    public function getQrCodeHash(): string  { return $this->qrCodeHash; }
}
?>