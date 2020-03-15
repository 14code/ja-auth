<?php


use I4code\JaAuth\ScopeEntity;
use PHPUnit\Framework\TestCase;

class ScopeEntityTest extends TestCase
{
    public function testConstructor()
    {
        $scope = new ScopeEntity();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\ScopeEntityInterface::class, $scope);
    }

}
