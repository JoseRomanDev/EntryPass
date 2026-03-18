<?php

namespace App\Domain\Entity;

class Event{
    public function __construct(
        private string $id,
        private string $title,
        private string $description,
        private \DateTimeImmutable $date,
        private float $price,
        private int $capacity,
        private bool $status = true
    ) {}

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function getPrice(): float { return $this->price; }
    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }
    public function getStatus(): bool { return $this->status; }
}
