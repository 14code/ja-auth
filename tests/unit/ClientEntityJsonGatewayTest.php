<?php


use I4code\JaAuth\ClientEntityJsonGateway;
use PHPUnit\Framework\TestCase;

class ClientEntityJsonGatewayTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryTrait;

    public function setUp(): void
    {
        $this->createClientJsonRepository();
    }


    public function testConstruct()
    {
        $encoder = new \I4code\JaApi\JsonEncoder();
        $gateway = new ClientEntityJsonGateway($this->file, $encoder);
        $this->assertInstanceOf(ClientEntityJsonGateway::class, $gateway);
        return $gateway;
    }


    /**
     * @param $gateway
     * @throws \I4code\JaApi\Exceptions\NoDatasourceException
     * @depends testConstruct
     */
    public function testRetrieveAll($gateway)
    {
        $this->assertInstanceOf(ClientEntityJsonGateway::class, $gateway);
        $items = $gateway->retrieveAll();
        $this->assertIsArray($items);
        $this->assertCount(count($this->clients), $items);
    }

}
