<?php

namespace App\Application\MessageHandler;

use App\Application\Message\SendPurchaseEmailMessage;
use App\Application\Port\EmailSenderInterface;
use App\Application\Port\PdfGeneratorInterface;
use App\Application\Port\QrCodeGeneratorInterface;
use App\Domain\Repository\PurchaseRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendPurchaseEmailHandler
{
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private QrCodeGeneratorInterface $qrCodeGenerator,
        private PdfGeneratorInterface $pdfGenerator,
        private EmailSenderInterface $emailSender
    ) {}

    public function __invoke(SendPurchaseEmailMessage $message): void
    {
        $purchase = $this->purchaseRepository->findById($message->purchaseId);

        if (!$purchase) {
            throw new \RuntimeException("Purchase {$message->purchaseId} not found.");
        }

        $tickets = $purchase->getTickets();
        
        echo "Iniciando generación de PDF para la compra " . $purchase->getId() . " con " . count($tickets) . " tickets...\n";

        $ticketData = [];

        // 1. Generar todos los QRs en formato base64
        foreach ($tickets as $ticket) {
            $base64Qr = $this->qrCodeGenerator->generateBase64($ticket->getQrCodeHash());

            $ticketData[] = [
                'id' => $ticket->getId(),
                'qr' => 'data:image/png;base64,' . $base64Qr
            ];
        }

        // 2. Generar el documento PDF
        $pdfHtml = $this->buildPdfHtml($purchase, $ticketData);
        $pdfContent = $this->pdfGenerator->generateFromHtml($pdfHtml);

        // 3. Preparar adjuntos e imágenes inline
        $logoPath = dirname(__DIR__, 3) . '/public/images/logo.png';
        
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

        // 4. Enviar UN único email
        $subject = '🎟️ Tus entradas para ' . $purchase->getEvent()->getTitle();
        $htmlBody = $this->buildEmailBody($purchase, count($tickets));

        $this->emailSender->send(
            $purchase->getUser()->getEmail(),
            $subject,
            $htmlBody,
            $attachments,
            $inlineImages
        );

        echo "Email único enviado con PDF adjunto para la compra " . $purchase->getId() . ".\n";
    }

    private function buildPdfHtml(\App\Domain\Entity\Purchase $purchase, array $ticketData): string
    {
        $event = $purchase->getEvent();
        $user = $purchase->getUser();

        $html = '<div style="font-family: Arial, sans-serif; color: #333333;">';
        $html .= '<div style="background-color: #1a7a8d; color: white; padding: 15px; border-radius: 5px; text-align: center;">';
        $html .= '<h1 style="margin: 0;">Entradas: ' . $event->getTitle() . '</h1>';
        $html .= '</div>';
        $html .= '<div style="padding: 15px 0;">';
        $html .= '<p><strong>Comprador:</strong> ' . $user->getName() . ' (' . $user->getEmail() . ')</p>';
        $html .= '<p><strong>Fecha Evento:</strong> ' . $event->getDate()->format('d/m/Y H:i') . '</p>';
        $html .= '<p><strong>ID Compra:</strong> ' . $purchase->getId() . '</p>';
        $html .= '</div>';
        $html .= '<hr style="border: 1px solid #1a7a8d; margin-bottom: 30px;">';

        foreach ($ticketData as $i => $data) {
            $html .= '<div style="text-align:center; margin-top:20px; padding: 20px; border: 2px solid #1a7a8d; border-radius: 10px; page-break-inside: avoid; background-color: #e9f2f4;">';
            $html .= '<h3 style="color: #1a7a8d; margin-top: 0;">Entrada #' . ($i + 1) . '</h3>';
            $html .= '<p style="color: #555; font-size: 14px;">ID Ticket: <code>' . $data['id'] . '</code></p>';
            $html .= '<img src="' . $data['qr'] . '" style="width: 250px; height: auto; margin-top: 15px;" />';
            $html .= '</div>';
            
            if ($i < count($ticketData) - 1) {
                $html .= '<div style="height: 30px;"></div>';
            }
        }
        $html .= '</div>';

        return $html;
    }

    private function buildEmailBody(\App\Domain\Entity\Purchase $purchase, int $ticketsCount): string
    {
        return sprintf(
            '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333333; border: 1px solid #e0e0e0; border-radius: 5px; overflow: hidden;">
                <div style="background-color: #f8fafb; padding: 20px; text-align: center; border-bottom: 3px solid #1a7a8d;">
                    <img src="cid:logo" alt="EntryPass Logo" style="max-height: 120px; margin-bottom: 10px;" />
                </div>
                <div style="background-color: #1a7a8d; color: white; padding: 15px; text-align: center;">
                    <h1 style="margin: 0; font-size: 22px;">¡Tu compra está confirmada!</h1>
                </div>
                <div style="padding: 20px;">
                    <p>Hola <strong>%s</strong>,</p>
                    <p>Tu compra para el evento <strong style="color: #1a7a8d;">%s</strong> el día <strong>%s</strong> se ha procesado correctamente.</p>
                    <div style="background-color: #e9f2f4; padding: 15px; border-left: 4px solid #1a7a8d; margin: 20px 0;">
                        <p style="margin: 0;">Adjunto a este correo encontrarás un archivo <strong>PDF con tus %d entrada(s)</strong>. Presenta el código QR de cada entrada (impreso o en el móvil) para acceder al evento.</p>
                    </div>
                    <p style="color: #666; font-size: 13px;">ID de compra: <code>%s</code></p>
                    <hr style="border: none; border-top: 1px solid #1a7a8d; margin: 20px 0;">
                    <p style="text-align: center; margin: 0;"><strong>¡Nos vemos en el evento!</strong><br><br><span style="color: #1a7a8d;"><em>El equipo de EntryPass</em></span></p>
                </div>
            </div>',
            $purchase->getUser()->getName(),
            $purchase->getEvent()->getTitle(),
            $purchase->getEvent()->getDate()->format('d/m/Y H:i'),
            $ticketsCount,
            $purchase->getId()
        );
    }
}
