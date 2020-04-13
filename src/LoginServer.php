<?php


namespace I4code\JaAuth;


use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoginServer
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function respondToLoginRequest(RequestInterface $request, ResponseInterface $response)
    {
        if('post' != $request->getMethod()) {
            throw new InvalidRequestException('Request with method POST required');
        }
        $requestData = $request->getParsedBody();
        if (!isset($requestData['login']) || !isset($requestData['password'])) {
            throw new InvalidRequestException('Values for login and password in request required');
        }
        $response = $response->withStatus(401);
        return $response;
    }

}