<?php


use I4code\JaAuth\ClientEntityFactory;
use PHPUnit\Framework\TestCase;

class ClientEntityFactoryTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createClientJsonRepository();
    }

    public function testCreate()
    {
        $factory = new ClientEntityFactory();
        $data = current($this->clients);
        $client = $factory->create($data);
        $this->assertInstanceOf(\I4code\JaAuth\ClientEntity::class, $client);
        $this->assertEquals($data->id, $client->getIdentifier());
    }

}
