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
use I4code\JaAuth\TestMocks\AuthorizationEnvironment;
use I4code\JaAuth\TestMocks\RepositoryMockTrait;
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

    protected $keyDir;

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
        $this->keyDir = __DIR__ . '/../../.keys';
        $this->setUpAuthorizationServer();
    }


    public function testServerInstance()
    {
        $this->assertInstanceOf(AuthorizationServer::class, $this->server);
    }


    public function testAuthorizePartOne()
    {
        $request = $this->generateAuthorizeRequest();

        $params = $request->getQueryParams();

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

        $headers = $response->getHeaders();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertNotEmpty($response->getHeader('Location'));

        $location = current($response->getHeader('Location'));

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

        $this->validateToken($data->access_token);
    }

    public function validateToken($token)
    {
        $this->assertNotEmpty($token);
        $this->storeLocalSession((object) ['token' => $token]);
    }

}
