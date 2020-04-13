<?php

namespace integration;

use I4code\JaApi\JsonEncoder;
use I4code\JaAuth\JsonGateway;
use I4code\JaAuth\Session;
use I4code\JaAuth\TestMocks\AuthorizationEnvironment;
use I4code\JaAuth\TestMocks\RepositoryMockTrait;
use I4code\JaAuth\UserEntityFactory;
use I4code\JaAuth\UserRepository;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $grantType = 'authorization_code';

    protected $server;
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
    }

    public function testLogin()
    {
        $login = 'user';
        $password = 'password';

        $query = [
            'login' => $login,
            'password' => $password
        ];

        $this->assertTrue(true);
    }

}
