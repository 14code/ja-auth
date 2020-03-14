<?php


use I4code\JaAuth\ClientEntity;
use PHPUnit\Framework\TestCase;

class ClientEntityTest extends TestCase
{

    public function testGetRedirectUri()
    {
        $client = new ClientEntity();
        $this->assertIsString($client->getRedirectUri());
        $this->assertNotEmpty($client->getRedirectUri());
    }

}
