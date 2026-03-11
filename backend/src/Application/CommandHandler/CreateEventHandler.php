<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateEventCommand;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler] 
class CreateEventHandler
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {}

    public function __invoke(CreateEventCommand $command): void
    {
        // 1. Convertimos los datos del Command (Application) al objeto Event (Domain)
        $event = new Event(
            $command->id,
            $command->title,
            $command->description,
            new \DateTimeImmutable($command->date),
            $command->price,
            $command->capacity,
            $command->status
        );

        // 2. Persistimos el evento usando el puerto (Interface)
        $this->repository->save($event);
    }
}