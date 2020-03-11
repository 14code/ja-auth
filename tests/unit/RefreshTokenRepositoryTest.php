<?php


use I4code\JaAuth\RefreshTokenRepository;
use PHPUnit\Framework\TestCase;

class RefreshTokenRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\RefreshTokenRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface::class, $repository);
    }

}
