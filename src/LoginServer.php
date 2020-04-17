<?php


namespace I4code\JaAuth;


use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginServer
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function respondToLoginRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        if('post' != $request->getMethod()) {
            throw LoginServerException::invalidRequestMethod('post', $request);
        }

        $requestData = $request->getParsedBody();
        if (!isset($requestData['login']) || empty($requestData['login'])) {
            throw LoginServerException::invalidRequest('login');
        }
        if (!isset($requestData['password']) || empty($requestData['password'])) {
            throw LoginServerException::invalidRequest('password');
        }
        if (!isset($requestData['redirect_uri']) || empty($requestData['redirect_uri'])) {
            throw LoginServerException::invalidRequest('redirect_uri');
        }

        $user = $this->userRepository->getUserEntityByLoginData($requestData['login'], $requestData['password']);
        if (!($user instanceof UserEntityInterface)) {
            throw LoginServerException::invalidCredentials();
        }

        $redirectResponse = new RedirectResponse();
        $redirectResponse->setRedirectUri($requestData['redirect_uri']);
        return $redirectResponse->generateHttpResponse($response);
    }

}