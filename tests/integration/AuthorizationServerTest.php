<?php

namespace integration;

use I4code\JaApi\ServerRequestFactory;
use I4code\JaAuth\AccessTokenRepository;
use I4code\JaAuth\AuthCodeRepository;
use I4code\JaAuth\ClientEntityFactory;
use I4code\JaAuth\ClientEntityJsonGateway;
use I4code\JaAuth\ClientRepository;
use I4code\JaAuth\RefreshTokenRepository;
use I4code\JaAuth\ScopeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use PHPUnit\Framework\TestCase;
use function I4code\JaAuth\generateRandomCodeChallenge;

class AuthorizationServerTest extends TestCase
{

    public function testAuthorize()
    {
        $keyDir = __DIR__ . '/../../.keys';

        $privateKey = $keyDir . '/private.key';
        $encryptionKey = file_get_contents($keyDir . '/encryption.key');

// Init our repositories
        $clientGateway = new ClientEntityJsonGateway();
        $clientFactory = new ClientEntityFactory();
        $clientRepository = new ClientRepository($clientGateway, $clientFactory); // instance of ClientRepositoryInterface

        $scopeRepository = new ScopeRepository(); // instance of ScopeRepositoryInterface
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

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        $this->assertInstanceOf(AuthorizationServer::class, $server);

        $codeChallenge = generateRandomCodeChallenge();

        $query = [
            'response_type' => 'code',
            'client_id' => 'lalala',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        ];
        $uri = '/authorize';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('get', $uri);
        $request = $request->withQueryParams($query);

        $authRequest = $server->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

    }

}
