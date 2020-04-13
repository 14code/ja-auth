<?php


namespace I4code\JaAuth\TestMocks;


use I4code\JaApi\ServerRequestFactory;
use I4code\JaAuth\AccessTokenRepository;
use I4code\JaAuth\AuthCodeRepository;
use I4code\JaAuth\ClientEntityFactory;
use I4code\JaAuth\ClientRepository;
use I4code\JaAuth\JsonGateway;
use I4code\JaApi\JsonEncoder;
use I4code\JaAuth\RefreshTokenRepository;
use I4code\JaAuth\ScopeEntityFactory;
use I4code\JaAuth\ScopeRepository;
use I4code\JaAuth\Session;
use I4code\JaAuth\UserEntityFactory;
use I4code\JaAuth\UserRepository;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nyholm\Psr7\Response;
use function I4code\JaAuth\extractParameterFromUrl;
use function I4code\JaAuth\generateRandomCodeChallenge;
use function I4code\JaAuth\generateRandomCodeVerifier;
use function I4code\JaAuth\generateState;

trait AuthorizationEnvironment
{

    public function setUpAuthorizationServer()
    {
        $this->createClientJsonRepository();
        $this->createScopeJsonRepository();
        $this->createUserJsonRepository();

        $this->codeVerifier = generateRandomCodeVerifier();
        $this->codeChallenge = generateRandomCodeChallenge($this->codeVerifier);
        $this->state = generateState();

        $privateKey = $this->keyDir . '/private.key';
        $encryptionKey = file_get_contents($this->keyDir . '/encryption.key');

        $this->redirectUri = 'notempty';

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


    public function generateLoginRequest($login, $password)
    {
        $query = [
            'login' => $login,
            'password' => $password
        ];
        $uri = '/login';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('post', $uri);

        // Use parsed body to mock requests!!!
        return $request->withParsedBody($query);
    }


    public function generateApprovedAuthorizationCode()
    {
        $request = $this->generateAuthorizationRequest();

        $params = $request->getQueryParams();

        $authRequest = $this->server->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

        // Mocking login of session user
        $client = $authRequest->getClient();
        $user = $this->userRepository->getUserEntityByUserCredentials($this->uniqueUser->login, $this->uniqueUser->password, $this->grantType, $client);

        // set user on auth request and approve
        $authRequest->setUser($user);
        $authRequest->setAuthorizationApproved(true);

        $response = new Response();
        $response = $this->server->completeAuthorizationRequest($authRequest, $response);

        $location = current($response->getHeader('Location'));
        return extractParameterFromUrl('code', $location);
    }


    public function generateAccessTokenRequest($code)
    {
        $query = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->uniqueClientId,
            'code' => $code,
            'redirect_uri' => $this->redirectUri, // should be allowed by client!!!I
            'code_verifier' => $this->codeVerifier,
        ];
        $uri = '/access_token';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('post', $uri);

        // Use parsed body to mock requests!!!
        return $request->withParsedBody($query);
    }

}