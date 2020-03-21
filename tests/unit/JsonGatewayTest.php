<?php


use I4code\JaAuth\JsonGateway;
use PHPUnit\Framework\TestCase;

class JsonGatewayTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createClientJsonRepository();
        $this->createScopeJsonRepository();
    }


    public function testConstruct()
    {
        $encoder = new \I4code\JaApi\JsonEncoder();
        $gateway = new JsonGateway($this->clientJsonFile, $encoder);
        $this->assertInstanceOf(JsonGateway::class, $gateway);
    }


    /**
     */
    public function testRetrieveAllClients()
    {
        $encoder = new \I4code\JaApi\JsonEncoder();
        $gateway = new JsonGateway($this->clientJsonFile, $encoder);
        $this->assertInstanceOf(JsonGateway::class, $gateway);
        $items = $gateway->retrieveAll();
        $this->assertIsArray($items);
        $this->assertCount(count($this->clients), $items);
    }


    /**
     */
    public function testRetrieveAllScopes()
    {
        $encoder = new \I4code\JaApi\JsonEncoder();
        $gateway = new JsonGateway($this->scopeJsonFile, $encoder);
        $this->assertInstanceOf(JsonGateway::class, $gateway);
        $items = $gateway->retrieveAll();
        $this->assertIsArray($items);
        $this->assertCount(count($this->scopes), $items);
    }

}
