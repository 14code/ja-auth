<?php


use I4code\JaAuth\AuthCodeRepository;
use PHPUnit\Framework\TestCase;

class AuthCodeRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\AuthCodeRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface::class, $repository);
        return $repository;
    }

    /**
     * @param $repository
     * @depends testConstruct
     */
    public function testGetNewAuthCode($repository)
    {
        $authCode = $repository->getNewAuthCode();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\AuthCodeEntityInterface::class, $authCode);
    }

}
