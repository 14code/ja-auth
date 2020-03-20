<?php


use I4code\JaAuth\ScopeEntityFactory;
use PHPUnit\Framework\TestCase;

class ScopeEntityFactoryTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createScopeJsonRepository();
    }

    public function testCreate()
    {
        $factory = new ScopeEntityFactory();
        $data = current($this->scopes);
        $scope = $factory->create($data);
        $this->assertInstanceOf(\I4code\JaAuth\ScopeEntity::class, $scope);
        $this->assertEquals($data->id, $scope->getIdentifier());
    }

}
