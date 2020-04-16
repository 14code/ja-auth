<?php


use I4code\JaAuth\LoginServer;
use PHPUnit\Framework\TestCase;

class LoginServerTest extends TestCase
{
    protected $loginServer;

    public function setUp(): void
    {
        $userMock = $this->createMock(\League\OAuth2\Server\Entities\UserEntityInterface::class);
        $userRepoMock = $this->createMock(\I4code\JaAuth\UserRepository::class);
        $userRepoMock->method('getUserEntityByLoginData')->willReturn($userMock);

        $this->loginServer = new LoginServer($userRepoMock);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(LoginServer::class, $this->loginServer);
    }

    public function testRespondToLoginRequest()
    {
        $queryData = [
            'login' => 'user',
            'password' => 'sfsdfd'
        ];

        $requestMock = $this->createMock(\Psr\Http\Message\ServerRequestInterface::class);
        $requestMock->method('getMethod')->willReturn('post');
        $requestMock->method('getParsedBody')->willReturn($queryData);

        $responseMock = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        $responseMock->method('withStatus')->willReturn($responseMock);

        $response = $this->loginServer->respondToLoginRequest($requestMock, $responseMock);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
    }
}
