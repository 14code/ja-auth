<?php

use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $gatewayMock = $this->createMock(\I4code\JaAuth\ClientEntityJsonGateway::class);
        $factoryMock = $this->createMock(\I4code\JaAuth\ClientEntityFactory::class);

        $repository = new \I4code\JaAuth\ClientRepository($gatewayMock, $factoryMock);
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class, $repository);
        return $repository;
    }

    /**
     * @depends testConstruct
     */
    public function testGetClientEntity($repository)
    {
        $clientId = 'testclient';
        $client = $repository->getClientEntity($clientId);
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ClientEntityInterface::class, $client);
    }

}
