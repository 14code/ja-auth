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

class AccessTokenTest extends TestCase
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


    public function testAccessToken()
    {
        $code = $this->generateApprovedAuthorizationCode();
        $request = $this->generateAccessTokenRequest($code);

        $response = new Response();
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
