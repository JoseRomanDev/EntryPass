<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends WebTestCase
{
    public function testListEventsIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/events');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateEventRequiresAdmin(): void
    {
        $client = static::createClient();
        
        // Sin token debe fallar (401 por JWT bundle o 403 por IsGranted si no hay user)
        $client->request('POST', '/api/events', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'title' => 'Test Event',
            'date' => '2025-12-01',
            'price' => 50.0,
            'capacity' => 100
        ]));

        $this->assertContains($client->getResponse()->getStatusCode(), [
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN
        ]);
    }
}
