<?php


use PHPUnit\Framework\TestCase;

class ResourceServerTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\MockConfigTrait;
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    protected $resourceServer;

    public function setUp(): void
    {
        $this->loadLocalSession();

        $accessTokenRepository = new \I4code\JaAuth\AccessTokenRepository();

        // Path to authorization server's public key
        $publicKeyPath = $this->getPublicKeyPath();

        $this->resourceServer = new \League\OAuth2\Server\ResourceServer(
            $accessTokenRepository,
            $publicKeyPath
        );
    }

    public function testServerInstance()
    {
        $this->assertInstanceOf(\League\OAuth2\Server\ResourceServer::class, $this->resourceServer);
    }


    public function testRequestWithoutToken()
    {
        $this->expectException(\League\OAuth2\Server\Exception\OAuthServerException::class);
        $uri = '/data';
        $serverRequestFactory = new \I4code\JaApi\ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest('get', $uri);
        $this->resourceServer->validateAuthenticatedRequest($serverRequest);
    }


    public function testRequestInvalidToken()
    {
        $this->expectException(\League\OAuth2\Server\Exception\OAuthServerException::class);
        $uri = '/data';
        $token = uniqid();

        $serverRequestFactory = new \I4code\JaApi\ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest('get', $uri);
        $serverRequest = $serverRequest->withHeader('authorization', $token);

        $this->resourceServer->validateAuthenticatedRequest($serverRequest);
    }


    public function testTokenVerify()
    {
        $uri = '/token';
        $token = $this->localSession->token;

        $serverRequestFactory = new \I4code\JaApi\ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest('get', $uri);
        $serverRequest = $serverRequest->withHeader('authorization', $token);

        $validatedRequest = $this->resourceServer->validateAuthenticatedRequest($serverRequest);

        $headers = $validatedRequest->getHeaders();
        $attributes = $validatedRequest->getAttributes();

        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $validatedRequest);
    }

}
