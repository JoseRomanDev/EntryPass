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
    public function setTitle(string $title): void { $this->title = $title; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $date): void { $this->date = $date; }
    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $capacity): void { $this->capacity = $capacity; }
    public function getStatus(): bool { return $this->status; }
    public function setStatus(bool $status): void { $this->status = $status; }
}
