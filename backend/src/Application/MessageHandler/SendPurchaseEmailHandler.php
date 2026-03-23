<?php

namespace App\Application\MessageHandler;

use App\Application\Message\SendPurchaseEmailMessage;
use App\Domain\Repository\PurchaseRepositoryInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

#[AsMessageHandler]
class SendPurchaseEmailHandler
{
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private MailerInterface             $mailer
    ) {}

    public function __invoke(SendPurchaseEmailMessage $message): void
    {
        $purchase = $this->purchaseRepository->findById($message->purchaseId);

        if (!$purchase) {
            throw new \RuntimeException("Purchase {$message->purchaseId} not found.");
        }

        echo "Iniciando envío de " . count($purchase->getTickets()) . " emails para la compra " . $purchase->getId() . "...\n";
        $i = 0;

        foreach ($purchase->getTickets() as $ticket) {
            // 1. Generar el código QR como PNG en memoria
            $qrCode = new QrCode(
                data: $ticket->getQrCodeHash(),
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer  = new PngWriter();
            $result  = $writer->write($qrCode);
            $pngData = $result->getString();

            // 2. Enviar email con el QR adjunto
            $email = (new Email())
                ->from('entrypass@entrypass.dev')
                ->to($purchase->getUser()->getEmail())
                ->subject('🎟️ Tu entrada para ' . $purchase->getEvent()->getTitle())
                ->html($this->buildEmailBody($purchase, $ticket))
                ->addPart(
                    (new DataPart($pngData, 'ticket_qr.png', 'image/png'))
                        ->asInline()
                );

            $this->mailer->send($email);

            echo sprintf("Enviado email %d de %d para el ticket: %s\n", ++$i, count($purchase->getTickets()), $ticket->getId());

            // Evitar límites de SMTP restrictivos (aumentamos a 3 segs para ir a lo super seguro)
            sleep(3);
        }
    }

    private function buildEmailBody(\App\Domain\Entity\Purchase $purchase, \App\Domain\Entity\Ticket $ticket): string
    {
        return sprintf(
            '<h1>¡Tu entrada está confirmada!</h1>
            <p>Hola <strong>%s</strong>,</p>
            <p>Tu compra para el evento <strong>%s</strong> el día <strong>%s</strong> ha sido confirmada.</p>
            <p>Adjunto encontrarás el código QR de tu entrada. Preséntalo en la entrada del evento.</p>
            <div style="text-align: center; margin: 20px 0;">
                <img src="cid:ticket_qr.png" alt="Código QR de tu entrada" style="max-width: 250px; height: auto;">
            </div>
            <p>ID de compra: <code>%s</code><br>ID de ticket: <code>%s</code></p>
            <p>¡Nos vemos en el evento!</p>
            <p><em>El equipo de EntryPass</em></p>',
            $purchase->getUser()->getName(),
            $purchase->getEvent()->getTitle(),
            $purchase->getEvent()->getDate()->format('d/m/Y H:i'),
            $purchase->getId(),
            $ticket->getId()
        );
    }
}
