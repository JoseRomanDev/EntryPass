<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Purchase
{
    public const STATUS_PENDING      = 'pending';
    public const STATUS_COMPLETED    = 'completed';
    public const STATUS_EMAIL_FAILED = 'email_failed';
    public const STATUS_CANCELLED    = 'cancelled';

    private Collection $tickets;

    public function __construct(
        private string           $id,
        private User             $user,
        private Event            $event,
        private int              $quantity,
        private float            $totalPrice,
        private string           $status = self::STATUS_PENDING,
        private DateTimeImmutable $purchasedAt = new DateTimeImmutable()
    ) {
        $this->tickets = new ArrayCollection();
    }

    // ── Getters ─────────────────────────────────────────────────────────────

    public function getId(): string             { return $this->id; }
    public function getUser(): User             { return $this->user; }
    public function getEvent(): Event           { return $this->event; }
    public function getQuantity(): int          { return $this->quantity; }
    public function getTotalPrice(): float      { return $this->totalPrice; }
    public function getStatus(): string         { return $this->status; }
    public function getPurchasedAt(): DateTimeImmutable { return $this->purchasedAt; }

    /** @return Collection<int, Ticket> */
    public function getTickets(): Collection    { return $this->tickets; }

    // ── Status transitions ───────────────────────────────────────────────────

    public function markCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
    }

    public function markEmailFailed(): void
    {
        $this->status = self::STATUS_EMAIL_FAILED;
    }

    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
    }

    // ── Collection helpers ───────────────────────────────────────────────────

    public function addTicket(Ticket $ticket): void
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
        }
    }
}
