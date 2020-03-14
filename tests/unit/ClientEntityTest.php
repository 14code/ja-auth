<?php


use I4code\JaAuth\ClientEntity;
use PHPUnit\Framework\TestCase;

class ClientEntityTest extends TestCase
{

    public function testIsConfidential()
    {

    }

    public function testGetRedirectUri()
    {
        $client = new ClientEntity();
        $this->assertIsString($client->getRedirectUri());
        $this->assertNotEmpty($client->getRedirectUri());
    }

    public function testGetName()
    {

    }

    public function testGetIdentifier()
    {

    }
}
