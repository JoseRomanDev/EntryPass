<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UpdateEventCommand;
use App\Domain\Repository\EventRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateEventHandler
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {}

    public function __invoke(UpdateEventCommand $command): void
    {
        $event = $this->repository->findById($command->id);

        if (!$event) {
            throw new \Exception("Event not found with ID: " . $command->id);
        }

        if ($command->title !== null) {
            $event->setTitle($command->title);
        }
        if ($command->description !== null) {
            $event->setDescription($command->description);
        }
        if ($command->date !== null) {
            $event->setDate(new \DateTimeImmutable($command->date));
        }
        if ($command->price !== null) {
            $event->setPrice($command->price);
        }
        if ($command->capacity !== null) {
            $event->setCapacity($command->capacity);
        }
        if ($command->status !== null) {
            $event->setStatus($command->status);
        }

        $this->repository->save($event);
    }
}
