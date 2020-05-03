<?php


use I4code\JaAuth\ClientEntity;
use PHPUnit\Framework\TestCase;

class ClientEntityTest extends TestCase
{

    public function testGetRedirectUri()
    {
        $clientId = uniqid('client');

        $client = new ClientEntity();
        $client->setIdentifier($clientId);

        $this->assertNull($client->getRedirectUri());

        $this->assertIsString($client->getIdentifier());
        $this->assertNotEmpty($client->getIdentifier());
    }

}
