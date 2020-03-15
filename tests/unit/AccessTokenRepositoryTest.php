<?php


use I4code\JaAuth\AccessTokenRepository;
use PHPUnit\Framework\TestCase;

class AccessTokenRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\AccessTokenRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface::class, $repository);
        return $repository;
    }

    /**
     * @param $repository
     * @depends testConstruct
     */
    public function testGetNewToken($repository)
    {
        $client = $this->createMock(\League\OAuth2\Server\Entities\ClientEntityInterface::class);
        $scopes = [];
        $userIdentifier = 'testUser';
        $token = $repository->getNewToken($client, $scopes, $userIdentifier);
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\AccessTokenEntityInterface::class, $token);
    }

}
