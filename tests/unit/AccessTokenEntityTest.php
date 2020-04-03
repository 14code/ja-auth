<?php


use I4code\JaAuth\AccessTokenEntity;
use PHPUnit\Framework\TestCase;

class AccessTokenEntityTest extends TestCase
{

    public function testGetClient()
    {
        $clientMock = $this->createMock(\League\OAuth2\Server\Entities\ClientEntityInterface::class);
        $accessToken = new AccessTokenEntity($clientMock);

        $client = $accessToken->getClient();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ClientEntityInterface::class, $client);
    }
}
