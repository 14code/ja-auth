<?php


use I4code\JaAuth\UserEntity;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testConstructor()
    {
        $user = new UserEntity();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\UserEntityInterface::class, $user);
    }

}
