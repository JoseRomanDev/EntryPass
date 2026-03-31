<?php

namespace App\Tests\Unit\Application\CommandHandler;

use App\Application\Command\PurchaseTicketCommand;
use App\Application\CommandHandler\PurchaseTicketHandler;
use App\Domain\Entity\Event;
use App\Domain\Entity\Purchase;
use App\Domain\Entity\User;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\Repository\PurchaseRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class PurchaseTicketHandlerTest extends TestCase
{
    private $eventRepository;
    private $userRepository;
    private $purchaseRepository;
    private $bus;
    private $handler;

    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->purchaseRepository = $this->createMock(PurchaseRepositoryInterface::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->handler = new PurchaseTicketHandler(
            $this->eventRepository,
            $this->userRepository,
            $this->purchaseRepository,
            $this->bus
        );
    }

    public function testPurchaseSuccess(): void
    {
        $user = new User('user-1', 'Test User', 'test@example.com', 'hashed_pass');
        $event = new Event('event-1', 'Test Event', 'Desc', new \DateTimeImmutable('2025-01-01'), 100.0, 10);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->eventRepository->method('findById')->willReturn($event);
        $this->purchaseRepository->method('countTicketsByUserAndEvent')->willReturn(0);

        $this->bus->expects($this->once())->method('dispatch')->willReturn(new Envelope(new \stdClass()));
        
        $command = new PurchaseTicketCommand('event-1', 2, 'test@example.com');
        $purchase = ($this->handler)($command);

        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertEquals(2, $purchase->getQuantity());
        $this->assertEquals(8, $event->getCapacity()); // 10 - 2
    }

    public function testPurchaseExceedsLimit(): void
    {
        $user = new User('user-1', 'Test User', 'test@example.com', 'hashed_pass');
        $event = new Event('event-1', 'Test Event', 'Desc', new \DateTimeImmutable('2025-01-01'), 100.0, 10);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->eventRepository->method('findById')->willReturn($event);
        $this->purchaseRepository->method('countTicketsByUserAndEvent')->willReturn(3);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Límite excedido');

        $command = new PurchaseTicketCommand('event-1', 2, 'test@example.com');
        ($this->handler)($command);
    }

    public function testPurchaseInsufficientCapacity(): void
    {
        $user = new User('user-1', 'Test User', 'test@example.com', 'hashed_pass');
        $event = new Event('event-1', 'Test Event', 'Desc', new \DateTimeImmutable('2025-01-01'), 100.0, 1);

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->eventRepository->method('findById')->willReturn($event);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No hay suficientes entradas');

        $command = new PurchaseTicketCommand('event-1', 2, 'test@example.com');
        ($this->handler)($command);
    }
}
