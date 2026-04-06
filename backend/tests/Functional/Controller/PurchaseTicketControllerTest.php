<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseTicketControllerTest extends WebTestCase
{
    public function testPurchaseRequiresAuthentication(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/purchases', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'eventId' => 'dummy-event-id',
            'quantity' => 2
        ]));

        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN
        ]);
    }
}
