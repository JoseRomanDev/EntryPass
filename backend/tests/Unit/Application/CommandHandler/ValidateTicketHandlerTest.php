<?php

namespace App\Tests\Unit\Application\CommandHandler;

use App\Application\Command\ValidateTicketCommand;
use App\Application\CommandHandler\ValidateTicketHandler;
use App\Domain\Entity\Ticket;
use App\Domain\Repository\TicketRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ValidateTicketHandlerTest extends TestCase
{
    private $ticketRepository;
    private $handler;

    protected function setUp(): void
    {
        $this->ticketRepository = $this->createMock(TicketRepositoryInterface::class);
        $this->handler = new ValidateTicketHandler($this->ticketRepository);
    }

    public function testValidateSuccess(): void
    {
        $ticket = $this->createMock(Ticket::class);
        $ticket->expects($this->once())->method('validateEntry');
        
        $this->ticketRepository->method('findByQrHash')->willReturn($ticket);
        $this->ticketRepository->expects($this->once())->method('save')->with($ticket);

        $command = new ValidateTicketCommand('valid-qr-hash');
        $result = ($this->handler)($command);

        $this->assertSame($ticket, $result);
    }

    public function testValidateNotFound(): void
    {
        $this->ticketRepository->method('findByQrHash')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ticket no encontrado');

        $command = new ValidateTicketCommand('invalid-qr-hash');
        ($this->handler)($command);
    }
}
