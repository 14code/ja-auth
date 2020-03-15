<?php

use I4code\JaAuth\AuthCodeEntity;
use PHPUnit\Framework\TestCase;

class AuthCodeEntityTest extends TestCase
{
    public function testConstructor()
    {
        $authcode = new AuthCodeEntity();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\AuthCodeEntityInterface::class, $authcode);
    }

}
