<?php


use I4code\JaAuth\AccessTokenRepository;
use PHPUnit\Framework\TestCase;

class AccessTokenRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\AccessTokenRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface::class, $repository);
    }

}
