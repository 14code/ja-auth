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
use I4code\JaAuth\UserEntity;
use League\OAuth2\Server\AuthorizationServer;
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

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        $this->assertInstanceOf(AuthorizationServer::class, $server);

        $codeVerifier = generateRandomCodeVerifier();
        $codeChallenge = generateRandomCodeChallenge($codeVerifier);

        $state = generateState();

        $redirectUri = 'redirect/to/me';
        $redirectUri = 'notempty';

        // ToDo: implement/mock auth with different clients -> valid/invalid
        // ToDo: implement/mock auth with different scopes -> valid/invalid
        // ToDo: Need scope

        $query = [
            'response_type' => 'code',
            'client_id' => $this->uniqueClientId,
            //'redirect_uri' => $redirectUri, // should be allowed by client!!!I
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'scope' => 'user archive',
            'state' => $state
        ];
        $uri = '/authorize';

        $serverRequestFactory = new ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('get', $uri);
        $request = $request->withQueryParams($query);

        $authRequest = $server->validateAuthorizationRequest($request);
        $this->assertInstanceOf(AuthorizationRequest::class, $authRequest);

        // verify user (login)
        // ToDo: implement/mock login => valid/invalid

        // set user on authorization request
        $user = new UserEntity();
        $authRequest->setUser($user);

        // ToDo: test with false approve
        // transform exception to http response
        // } catch (OAuthServerException $exception) {
        //        // All instances of OAuthServerException can be formatted into a HTTP response
        //        return $exception->generateHttpResponse($response);
        //$authRequest->setAuthorizationApproved(false);

        $authRequest->setAuthorizationApproved(true);

        $response = new Response();
        $response = $server->completeAuthorizationRequest($authRequest, $response);


        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertNotEmpty($response->getHeader('Location'));

        $location = current($response->getHeader('Location'));

        //error_log($location);

        $this->assertEquals($state, extractParameterFromUrl('state', $location));

        $code = extractParameterFromUrl('code', $location);
        $this->assertNotEmpty($code);

        $query = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->uniqueClientId,
            'code' => $code,
            'redirect_uri' => $redirectUri, // should be allowed by client!!!I
            'code_verifier' => $codeVerifier,
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

        $response = $server->respondToAccessTokenRequest($request, $response);
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
