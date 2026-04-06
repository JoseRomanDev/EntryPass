<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ValidateTicketControllerTest extends WebTestCase
{
    public function testValidateTicketRequiresAuthentication(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/tickets/validate', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'qrCodeHash' => 'dummy-qr-hash'
        ]));

        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN
        ]);
    }
}
