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

    public function testTokenVerify()
    {
        $this->assertInstanceOf(\League\OAuth2\Server\ResourceServer::class, $this->resourceServer);

        $uri = '/token';
        $token = $this->localSession->token;

        $query = [
        ];
        $serverRequestFactory = new \I4code\JaApi\ServerRequestFactory();
        $request = $serverRequestFactory->createTestRequest('get', $uri);
        $request = $request->withQueryParams($query);
        $request = $request->withHeader('authorization', $token);

        $response = $this->resourceServer->validateAuthenticatedRequest($request);

        error_log(print_r($response->getHeaders(), true));
        error_log(print_r($response->getAttributes(), true));

        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $response);
    }

}
