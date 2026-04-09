<?php

namespace App\Application\MessageHandler;

use App\Application\Message\SendPurchaseEmailMessage;
use App\Application\Port\EmailSenderInterface;
use App\Application\Port\PdfGeneratorInterface;
use App\Application\Port\QrCodeGeneratorInterface;
use App\Domain\Repository\PurchaseRepositoryInterface;
use App\Domain\Entity\Purchase; 
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler]
class SendPurchaseEmailHandler
{
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private QrCodeGeneratorInterface $qrCodeGenerator,
        private PdfGeneratorInterface $pdfGenerator,
        private EmailSenderInterface $emailSender,
        private Environment $twig,
        private LoggerInterface $logger,
        private string $projectDir
    ) {}

    public function __invoke(SendPurchaseEmailMessage $message): void
    {
        $purchase = $this->purchaseRepository->findById($message->purchaseId);

        if (!$purchase) {
            $this->logger->error("Purchase {purchaseId} not found.", ['purchaseId' => $message->purchaseId]);
            throw new \RuntimeException("Purchase {$message->purchaseId} not found.");
        }

        $tickets = $purchase->getTickets();
        
        $this->logger->info("Iniciando generación de PDF para la compra {purchaseId} con {count} tickets.", [
            'purchaseId' => $purchase->getId(),
            'count' => count($tickets)
        ]);

        $ticketData = [];

        // 1. Generar todos los QRs en formato base64
        foreach ($tickets as $ticket) {
            $base64Qr = $this->qrCodeGenerator->generateBase64($ticket->getQrCodeHash());

            $ticketData[] = [
                'id' => $ticket->getId(),
                'qr' => 'data:image/png;base64,' . $base64Qr
            ];
        }

        // 2. Generar el documento PDF usando Twig
        $logoPath = $this->projectDir . '/public/images/logo.png';
        $logoBase64 = base64_encode(file_get_contents($logoPath));

        $pdfHtml = $this->twig->render('pdf/tickets.html.twig', [
            'purchase' => $purchase,
            'event' => $purchase->getEvent(),
            'user' => $purchase->getUser(),
            'ticketData' => $ticketData,
            'logoBase64' => 'data:image/png;base64,' . $logoBase64
        ]);
        $pdfContent = $this->pdfGenerator->generateFromHtml($pdfHtml);

        // 3. Preparar adjuntos e imágenes inline
        $attachments = [
            [
                'content' => $pdfContent,
                'name' => 'entradas_entrypass.pdf',
                'type' => 'application/pdf'
            ]
        ];

        $inlineImages = [
            [
                'path' => $logoPath,
                'cid' => 'logo'
            ]
        ];

        // 4. Enviar UN único email usando Twig
        $subject = '🎟️ Tus entradas para ' . $purchase->getEvent()->getTitle();
        $htmlBody = $this->twig->render('emails/purchase_confirmation.html.twig', [
            'purchase' => $purchase,
            'event' => $purchase->getEvent(),
            'user' => $purchase->getUser(),
            'ticketsCount' => count($tickets)
        ]);

        $this->emailSender->send(
            $purchase->getUser()->getEmail(),
            $subject,
            $htmlBody,
            $attachments,
            $inlineImages
        );

        $this->logger->info("Email enviado exitosamente para la compra {purchaseId}.", ['purchaseId' => $purchase->getId()]);
    }
}
