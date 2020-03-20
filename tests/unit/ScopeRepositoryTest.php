<?php


use I4code\JaAuth\ScopeRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;

class ScopeRepositoryTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createScopeJsonRepository();

        $gatewayMock = $this->createMock(\I4code\JaAuth\ScopeEntityJsonGateway::class);
        $gatewayMock->method('retrieveAll')->willReturn($this->scopes);

        $scopeMock = $this->createMock(\I4code\JaAuth\ScopeEntity::class);
        $scopeMock->method('getIdentifier')->willReturn($this->uniqueScopeId);

        $factoryMock = $this->createMock(\I4code\JaAuth\ScopeEntityFactory::class);
        $factoryMock->method('create')->willReturn($scopeMock);

        $this->repository = new \I4code\JaAuth\ScopeRepository($gatewayMock, $factoryMock);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\ScopeRepositoryInterface::class, $this->repository);
    }

    public function testFindAll()
    {
        $scopes = $this->repository->findAll();
        $this->assertCount(count($this->scopes), $scopes);
    }

    public function testGetScopeEntityByIdentifier()
    {
        $scope = $this->repository->getScopeEntityByIdentifier($this->uniqueScopeId);
        //error_log(print_r($scope, true));
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ScopeEntityInterface::class, $scope);
        $this->assertEquals($this->uniqueScopeId, $scope->getIdentifier());
    }

    public function testFinalizeScopes()
    {
        //finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
        $scopes = [];
        $client = $this->createMock(ClientEntityInterface::class);
        $userId = 'testUserId';
        $grantType = 'authorize_code';

        $scopes = $this->repository->finalizeScopes($scopes, $grantType, $client);
        $this->assertIsArray($scopes);
    }

}
