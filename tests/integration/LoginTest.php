<?php
ini_set('session.use_trans_sid', false);
ini_set('session.use_cookies', false);
ini_set('session.use_only_cookies', true);
ini_set('session.cache_limiter', '');

use I4code\JaApi\JsonEncoder;
use I4code\JaAuth\JsonGateway;
use I4code\JaAuth\LoginServer;
use I4code\JaAuth\LoginServerException;
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

    /**
     * ToDo:
     *  - session handling / which data should response contain?
     *  - store user id in session
     *  - test to receive user id via session
     *  - extract session cookie from response
     *  - add session cookie to request
     *  - verify request with session cookie
     */
    public function testLogin()
    {
        $this->destroySession();

        $login = $this->uniqueUser->login;
        $password = $this->uniqueUser->password;

        $redirectUri = '/my_redirect_target?client=client' . uniqid();

        $request = $this->generateLoginRequest($login, $password, $redirectUri);

        $response = new Response();
        $response = $this->loginServer->respondToLoginRequest($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals([$redirectUri], $response->getHeader('Location'));
        $this->assertTrue($response->hasHeader('Set-Cookie'));
        $this->assertNotEmpty($response->getHeader('Set-Cookie'));
    }

    /**
     * Invalid login should throw exception
     */
    public function testInvalidLogin()
    {
        $this->destroySession();

        $this->expectException(LoginServerException::class);

        $login = 'invalidUser';
        $password = 'invalidPass';

        $redirectUri = '/my_redirect_target?client=client' . uniqid();

        $request = $this->generateLoginRequest($login, $password, $redirectUri);

        $response = new Response();
        $response = $this->loginServer->respondToLoginRequest($request, $response);
    }


    public function testLoginWithSession()
    {
        $this->destroySession();

        $login = $this->uniqueUser->login;
        $password = $this->uniqueUser->password;

        $redirectUri = '/my_redirect_target?client=client' . uniqid();

        $request = $this->generateLoginRequest($login, $password, $redirectUri);

        $response = new Response();
        $response = $this->loginServer->respondToLoginRequest($request, $response);

        $sessionName = 'JaLoginSession';
        $sessionResponse = new \I4code\JaAuth\SessionCookieResponse();
        $sessionResponse->setSessionName($sessionName);
        $sessionId = $sessionResponse->extractSessionIdFromResponse($response);

        $this->assertNotEmpty($sessionId);

        $this->closeSession();

        $request = $this->generateSessionLoginRequest($sessionName, $sessionId);

        $response = new Response();
        $response = $this->loginServer->respondToLoginRequest($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(['/'], $response->getHeader('Location'));
        $this->assertTrue($response->hasHeader('Set-Cookie'));
        $this->assertNotEmpty($response->getHeader('Set-Cookie'));
    }

}
