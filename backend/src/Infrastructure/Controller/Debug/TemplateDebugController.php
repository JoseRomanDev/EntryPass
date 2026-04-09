<?php

namespace App\Infrastructure\Controller\Debug;

use App\Domain\Entity\Event;
use App\Domain\Entity\Purchase;
use App\Domain\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

/**
 * CONTROLADOR TEMPORAL DE DEBUG (PARA ELIMINAR ANTES DE ENTREGA)
 * Permite previsualizar los correos y PDFs en el navegador.
 */
class TemplateDebugController extends AbstractController
{
    #[Route('/debug/email', name: 'debug_email', methods: ['GET'])]
    public function previewEmail(): Response
    {
        $mockData = $this->createMockData();

        $logoPath = $this->getParameter('kernel.project_dir') . '/public/images/logo.png';
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        return $this->render('emails/purchase_confirmation.html.twig', [
            'user' => $mockData['user'],
            'event' => $mockData['event'],
            'purchase' => $mockData['purchase'],
            'ticketsCount' => $mockData['purchase']->getQuantity(),
            // En el navegador usamos base64 en lugar de cid:logo para previsualizar
            'logo_debug' => $logoBase64
        ]);
    }

    #[Route('/debug/pdf', name: 'debug_pdf', methods: ['GET'])]
    public function previewPdf(): Response
    {
        $mockData = $this->createMockData();

        $logoPath = $this->getParameter('kernel.project_dir') . '/public/images/logo.png';
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // Simulamos ticketData
        $ticketData = [];
        for ($i = 0; $i < $mockData['purchase']->getQuantity(); $i++) {
            $ticketData[] = [
                'id' => Uuid::v4()->toRfc4122(),
                'qr' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=DEBUG_QR' 
            ];
        }

        return $this->render('pdf/tickets.html.twig', [
            'user' => $mockData['user'],
            'event' => $mockData['event'],
            'purchase' => $mockData['purchase'],
            'ticketData' => $ticketData,
            'logoBase64' => $logoBase64 ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null
        ]);
    }

    private function createMockData(): array
    {
        $user = new User(
            Uuid::v4()->toRfc4122(),
            'Usuario de Prueba',
            'test@example.com',
            'password'
        );

        $event = new Event(
            Uuid::v4()->toRfc4122(),
            'Gran Concierto EntryPass 2026',
            'Una descripción increíble para un evento de prueba.',
            new DateTimeImmutable('+1 month'),
            85.50,
            500
        );

        $purchase = new Purchase(
            Uuid::v4()->toRfc4122(),
            $user,
            $event,
            2,
            171.00
        );

        return [
            'user' => $user,
            'event' => $event,
            'purchase' => $purchase
        ];
    }
}
