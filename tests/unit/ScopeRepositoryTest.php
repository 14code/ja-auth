<?php


use I4code\JaAuth\ScopeRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;

class ScopeRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\ScopeRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\ScopeRepositoryInterface::class, $repository);
        return $repository;
    }

    /**
     * @param $repository
     * @depends testConstruct
     */
    public function testGetScopeEntityByIdentifier($repository)
    {
        $id = 'testScope';
        $scope = $repository->getScopeEntityByIdentifier($id);
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ScopeEntityInterface::class, $scope);
    }

    /**
     * @param $repository
     * @depends testConstruct
     */
    public function testFinalizeScopes($repository)
    {
        //finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
        $scopes = [];
        $client = $this->createMock(ClientEntityInterface::class);
        $userId = 'testUserId';
        $grantType = 'authorize_code';

        $scopes = $repository->finalizeScopes($scopes, $grantType, $client);
        $this->assertIsArray($scopes);
    }

}
