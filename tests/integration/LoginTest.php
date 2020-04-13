<?php

namespace integration;

use I4code\JaApi\JsonEncoder;
use I4code\JaAuth\JsonGateway;
use I4code\JaAuth\LoginServer;
use I4code\JaAuth\Session;
use I4code\JaAuth\TestMocks\AuthorizationEnvironment;
use I4code\JaAuth\TestMocks\RepositoryMockTrait;
use I4code\JaAuth\UserEntityFactory;
use I4code\JaAuth\UserRepository;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoginTest extends TestCase
{
    protected $grantType = 'authorization_code';

    protected $loginServer;

    protected $redirectUri;
    protected $codeVerifier;
    protected $codeChallenge;
    protected $state;

    protected $userRepository;
    protected $session;

    use RepositoryMockTrait;
    use AuthorizationEnvironment;

    public function setUp(): void
    {
        $this->createUserJsonRepository();

// Init our repositories
        $encoder = new JsonEncoder();

        $userGateway = new JsonGateway($this->userJsonFile, $encoder);
        $userFactory = new UserEntityFactory();
        $this->userRepository = new UserRepository($userGateway, $userFactory); // instance of UserRepositoryInterface

        $this->session = new Session();

        $this->loginServer = new LoginServer($this->userRepository);
    }

    public function testLogin()
    {
        $login = 'user';
        $password = 'password';

        $request = $this->generateLoginRequest($login, $password);

        $response = new Response();
        $response = $this->loginServer->respondToLoginRequest($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

}
