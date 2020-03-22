<?php

namespace integration;

use I4code\JaApi\JsonEncoder;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaAuth\AccessTokenRepository;
use I4code\JaAuth\AuthCodeRepository;
use I4code\JaAuth\ClientEntityFactory;
use I4code\JaAuth\ClientRepository;
use I4code\JaAuth\JsonGateway;
use I4code\JaAuth\RefreshTokenRepository;
use I4code\JaAuth\ScopeEntityFactory;
use I4code\JaAuth\ScopeRepository;
use I4code\JaAuth\Session;
use I4code\JaAuth\UserEntity;
use I4code\JaAuth\UserEntityFactory;
use I4code\JaAuth\UserRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function I4code\JaAuth\extractParameterFromUrl;
use function I4code\JaAuth\generateRandomCodeChallenge;
use function I4code\JaAuth\generateRandomCodeVerifier;
use function I4code\JaAuth\generateState;

class AuthorizationServerTest extends TestCase
{
    protected $grantType = 'authorization_code';

    protected $server;
    protected $redirectUri;
    protected $codeVerifier;
    protected $codeChallenge;
    protected $state;

    protected $userRepository;
    protected $session;

    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createClientJsonRepository();
        $this->createScopeJsonRepository();
        $this->createUserJsonRepository();

        $this->codeVerifier = generateRandomCodeVerifier();
        $this->codeChallenge = generateRandomCodeChallenge($this->codeVerifier);
        $this->state = generateState();

        $keyDir = __DIR__ . '/../../.keys';

        $this->redirectUri = 'notempty';

        $privateKey = $keyDir . '/private.key';
        $encryptionKey = file_get_contents($keyDir . '/encryption.key');

// Init our repositories
        $encoder = new JsonEncoder();

        $clientGateway = new JsonGateway($this->clientJsonFile, $encoder);
        $clientFactory = new ClientEntityFactory();
        $clientRepository = new ClientRepository($clientGateway, $clientFactory); // instance of ClientRepositoryInterface

        $scopeGateway = new JsonGateway($this->scopeJsonFile, $encoder);
        $scopeFactory = new ScopeEntityFactory();
        $scopeRepository = new ScopeRepository($scopeGateway, $scopeFactory); // instance of ScopeRepositoryInterface

        $userGateway = new JsonGateway($this->userJsonFile, $encoder);
        $userFactory = new UserEntityFactory();
        $this->userRepository = new UserRepository($userGateway, $userFactory); // instance of UserRepositoryInterface

        $accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
        $authCodeRepository = new AuthCodeRepository(); // instance of AuthCodeRepositoryInterface
        $refreshTokenRepository = new RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface

        $this->session = new Session();

// Setup the authorization server
        $this->server = new \League\OAuth2\Server\AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );
    }

    public function generateAuthorizationRequest()
    {
        $query = [
            'response_type' => 'code',
            'client_id' => $this->uniqueClientId,
            'redirect_uri' => $this->redirectUri, // should be allowed by client!!!I
            'code_challenge' => $this->codeChallenge,
            'code_challenge_method' => 'S256',
            'scope' => 'user archive',
            'state' => $this->state
        ];
        $uri = '/authorize';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('get', $uri);
        $request = $request->withQueryParams($query);

        return $request;
    }

    public function testServerInstance()
    {
        $this->assertInstanceOf(AuthorizationServer::class, $this->server);
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

    public function testAuthorizationNoUser()
    {
        //$this->expectException(OAuthServerException::class);

        $request = $this->generateAuthorizationRequest();

        $authRequest = $this->server->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

        $authRequest->setUser($this->session->getUser());
        $authRequest->setAuthorizationApproved($this->session->userIsApproved());

        $response = null;
        try {
            $response = new Response();
            $this->server->completeAuthorizationRequest($authRequest, $response);
        }
        catch (OAuthServerException $e) {
            $return_to = $request->getUri() . '?' . http_build_query($request->getQueryParams());
            $redirectUri = '/login?client_id=' . $authRequest->getClient()->getIdentifier()
                . '&return_to=' . urlencode($return_to);
            $response = $e->generateHttpResponse($response);
            $response = $response->withHeader('Location', $redirectUri);
        }
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        //error_log(print_r($response->getHeaders(), true));
    }

    public function testAuthorizePartOne()
    {
        $request = $this->generateAuthorizationRequest();

        $authRequest = $this->server->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

        // ToDo: test with logged in user without permissions
        // Mocking login of session user
        $client = $authRequest->getClient();
        $user = $this->userRepository->getUserEntityByUserCredentials($this->uniqueUser->login, $this->uniqueUser->password, $this->grantType, $client);
        $this->session->setUser($user);

        // set user on auth request and approve
        $authRequest->setUser($this->session->getUser());
        $authRequest->setAuthorizationApproved($this->session->userIsApproved());

        $response = new Response();
        $response = $this->server->completeAuthorizationRequest($authRequest, $response);


        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertNotEmpty($response->getHeader('Location'));

        $location = current($response->getHeader('Location'));

        //error_log($location);

        $this->assertEquals($this->state, extractParameterFromUrl('state', $location));

        $code = extractParameterFromUrl('code', $location);
        $this->assertNotEmpty($code);

        $this->authorizePartTwo($code);
    }

    public function authorizePartTwo($code)
    {
        $query = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->uniqueClientId,
            'code' => $code,
            'redirect_uri' => $this->redirectUri, // should be allowed by client!!!I
            'code_verifier' => $this->codeVerifier,
            //'code_challenge' => $codeChallenge,
            //'code_challenge_method' => 'S256',
            //'scope' => 'user archive',
            //'state' => $state
        ];
        /*
    grant_type with the value of authorization_code
    client_id with the client identifier
    client_secret with the client secret
    redirect_uri with the same redirect URI the user was redirect back to
    code with the authorization code from the query string
*/
        $uri = '/access_token';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('post', $uri);
        //$body = $request->getBody();
        //$body->write(json_encode($query));
        //$request = $request->withBody($body);

        // Use parsed body to mock requests!!!
        $request = $request->withParsedBody($query);

        $response = new Response();

        // ToDo: Where is the refresh token?

        $response = $this->server->respondToAccessTokenRequest($request, $response);
        $body = $response->getBody();
        $this->assertJson($body);
        $data = json_decode($body);
        $this->assertObjectHasAttribute('token_type', $data);
        $this->assertEquals('Bearer', $data->token_type);
        $this->assertObjectHasAttribute('expires_in', $data);
        $this->assertEquals(3600, $data->expires_in);
        $this->assertObjectHasAttribute('access_token', $data);
        $this->assertNotEmpty($data->access_token);

    }

}
