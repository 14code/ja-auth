<?php

use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    protected $uniqueClientId;
    protected $repository;

    public function setUp(): void
    {
        $this->createClientJsonRepository();

        $gatewayMock = $this->createMock(\I4code\JaAuth\ClientEntityJsonGateway::class);
        $gatewayMock->method('retrieveAll')->willReturn($this->clients);

        $clientMock = $this->createMock(\I4code\JaAuth\ClientEntity::class);
        $clientMock->method('getIdentifier')->willReturn($this->uniqueClientId);

        $factoryMock = $this->createMock(\I4code\JaAuth\ClientEntityFactory::class);
        $factoryMock->method('create')->willReturn($clientMock);

        $this->repository = new \I4code\JaAuth\ClientRepository($gatewayMock, $factoryMock);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class, $this->repository);
    }

    public function testFindAll()
    {
        $clients = $this->repository->findAll();
        $this->assertCount(count($this->clients), $clients);
    }

    public function testGetClientEntity()
    {
        $client = $this->repository->getClientEntity($this->uniqueClientId);
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ClientEntityInterface::class, $client);
        $this->assertEquals($this->uniqueClientId, $client->getIdentifier());
    }

}
