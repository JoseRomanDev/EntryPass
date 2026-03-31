<?php

namespace App\Domain\Repository;
use App\Domain\Entity\Event;

interface EventRepositoryInterface{
    public function save(Event $event):void;
    public function findAll():array;
    public function findActive(): array;
    public function findById(string $id): ?Event;
    public function delete(Event $event): void;
}
?>
