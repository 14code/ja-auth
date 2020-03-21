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
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use PHPUnit\Framework\TestCase;
use function I4code\JaAuth\generateRandomCodeChallenge;
use function I4code\JaAuth\generateRandomCodeVerifier;

class AuthCodeGrantTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    public function setUp(): void
    {
        $this->createClientJsonRepository();
        $this->createScopeJsonRepository();
    }

    public function testAuthorize()
    {
        $keyDir = __DIR__ . '/../../.keys';

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

        $accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
        $authCodeRepository = new AuthCodeRepository(); // instance of AuthCodeRepositoryInterface
        $refreshTokenRepository = new RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface

// Setup the authorization server
        $server = new \League\OAuth2\Server\AuthorizationServer(
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

        $grant->setClientRepository($clientRepository);

        $this->assertInstanceOf(AuthCodeGrant::class, $grant);

        $codeVerifier = generateRandomCodeVerifier();
        $codeChallenge = generateRandomCodeChallenge($codeVerifier);

        $query = [
            'response_type' => 'code',
            'client_id' => $this->uniqueClientId,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        ];
        $uri = '/authorize';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('get', $uri);
        $request = $request->withQueryParams($query);

        $this->assertTrue($grant->canRespondToAuthorizationRequest($request));

        $authRequest = $grant->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

    }

}
