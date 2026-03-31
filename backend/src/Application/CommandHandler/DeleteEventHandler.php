<?php

namespace App\Application\CommandHandler;

use App\Application\Command\DeleteEventCommand;
use App\Domain\Repository\EventRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteEventHandler
{
    public function __construct(
        private EventRepositoryInterface $repository
    ) {}

    public function __invoke(DeleteEventCommand $command): void
    {
        $event = $this->repository->findById($command->id);

        if (!$event) {
            throw new \Exception("Event not found with ID: " . $command->id);
        }

        // Baja lógica: Ponemos el estado a false (inactivo)
        $event->setStatus(false);
        $this->repository->save($event);
    }
}
