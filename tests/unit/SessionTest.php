<?php


use I4code\JaAuth\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{

    public function testGetUser()
    {
        $session = new Session();
        $user = $session->getUser();
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\UserEntityInterface::class, $user);
    }

    public function testUserIsApproved()
    {
        $session = new Session();
        $this->assertFalse($session->userIsApproved());
    }

}
