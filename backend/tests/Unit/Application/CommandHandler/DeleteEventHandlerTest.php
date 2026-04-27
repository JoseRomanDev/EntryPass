<?php

namespace App\Tests\Unit\Application\CommandHandler;

use App\Application\Command\DeleteEventCommand;
use App\Application\CommandHandler\DeleteEventHandler;
use App\Domain\Entity\Event;
use App\Domain\Repository\EventRepositoryInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class DeleteEventHandlerTest extends TestCase
{
    public function testDeleteEventSoftDelete(): void
    {
        $repository = $this->createMock(EventRepositoryInterface::class);
        $handler = new DeleteEventHandler($repository);

        $event = new Event('event-1', 'Test', 'Desc', new \DateTimeImmutable(), 100.0, 10);
        
        $repository->method('findById')->willReturn($event);
        
        // Verificamos que se llame a save y NO a delete
        $repository->expects($this->once())->method('save')->with($this->callback(function (Event $e) {
            return $e->getStatus() === false;
        }));
        $repository->expects($this->never())->method('delete');

        $command = new DeleteEventCommand('event-1');
        ($handler)($command);

        $this->assertFalse($event->getStatus());
    }
}
