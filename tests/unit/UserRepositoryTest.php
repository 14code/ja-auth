<?php


use I4code\JaAuth\UserRepository;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createUserJsonRepository();

        $gatewayMock = $this->createMock(\I4code\JaAuth\JsonGateway::class);
        $gatewayMock->method('retrieveAll')->willReturn($this->users);

        $userMock = $this->createMock(\I4code\JaAuth\UserEntity::class);
        $userMock->method('getIdentifier')->willReturn($this->uniqueUser->id);
        $userMock->method('getLogin')->willReturn($this->uniqueUser->login);
        $userMock->method('getPassword')->willReturn($this->uniqueUser->password);
        $userMock->method('getEmail')->willReturn($this->uniqueUser->email);

        $factoryMock = $this->createMock(\I4code\JaAuth\UserEntityFactory::class);
        $factoryMock->method('create')->willReturn($userMock);

        $this->repository = new \I4code\JaAuth\UserRepository($gatewayMock, $factoryMock);
    }

    public function testFindAll()
    {
        $users = $this->repository->findAll();
        $this->assertCount(count($this->users), $users);
    }

    public function testGetUserEntityByUserCredentials()
    {
        $userData = $this->uniqueUser;

        $grant = 'invalid';
        $client = $this->createMock(\I4code\JaAuth\ClientEntity::class);

        $user = $this->repository->getUserEntityByUserCredentials($userData->login, $userData->password, $grant, $client);
        $this->assertInstanceOf(\I4code\JaAuth\UserEntity::class, $user);
        $this->assertEquals($userData->id, $user->getIdentifier());
    }

    public function testGetUserEntityByUserCredentialsWithEmail()
    {
        $userData = $this->uniqueUser;

        $grant = 'invalid';
        $client = $this->createMock(\I4code\JaAuth\ClientEntity::class);

        $user = $this->repository->getUserEntityByUserCredentials($userData->email, $userData->password, $grant, $client);
        $this->assertInstanceOf(\I4code\JaAuth\UserEntity::class, $user);
        $this->assertEquals($userData->id, $user->getIdentifier());
    }

}
