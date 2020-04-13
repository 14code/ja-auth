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

class AuthorizeTest extends TestCase
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

    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;
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
    }


    public function testAuthorizeValidUser()
    {
        $request = $this->generateAuthorizationRequest();

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

    }

}
