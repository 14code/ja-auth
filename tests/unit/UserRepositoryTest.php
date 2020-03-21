<?php


use I4code\JaAuth\UserRepository;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{

    public function testGetUserEntityByUserCredentials()
    {
        $repository = new UserRepository();
        $user = 'user';
        $password = 'pass';
        $grant = 'invalid';
        $client = $this->createMock(\I4code\JaAuth\ClientEntity::class);

        $user = $repository->getUserEntityByUserCredentials($user, $password, $grant, $client);
        $this->assertInstanceOf(\I4code\JaAuth\UserEntity::class, $user);
    }
}
