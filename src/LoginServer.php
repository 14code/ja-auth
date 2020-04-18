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
        $sessionName = 'JaLoginSession';

        $loggedIn = false;

        // If logged in redirect
        // if not logged in show login form
        if('get' == $request->getMethod()) {
            $sessionId = extractCookieValueFromRequest($sessionName, $request);
            $redirectUri = '/';

            if (empty($sessionId)) {
                throw LoginServerException::invalidRequest('sessionId');
            }
            session_id($sessionId);
            session_start();
            if (isset($_SESSION['userId'])) {
                $userId = $_SESSION['userId'];
                $user = $this->userRepository->getUserEntityByIdentifier($userId);
                if (!($user instanceof UserEntityInterface)) {
                    throw LoginServerException::invalidUser();
                }
                $loggedIn = true;
            }
        }

        if('post' == $request->getMethod()) {
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

            $redirectUri = $requestData['redirect_uri'];

            $user = $this->userRepository->getUserEntityByLoginData($requestData['login'], $requestData['password']);

            if (!($user instanceof UserEntityInterface)) {
                throw LoginServerException::invalidCredentials();
            }

            session_start();
            $_SESSION['userId'] = $user->getIdentifier();
            $sessionId = session_id();

            $loggedIn = true;
        }

        if ($loggedIn) {
            $sessionResponse = new SessionCookieResponse();
            $sessionResponse->setSessionId($sessionId);
            $sessionResponse->setSessionName($sessionName);
            $response = $sessionResponse->generateHttpResponse($response);

            $redirectResponse = new RedirectResponse();
            $redirectResponse->setRedirectUri($redirectUri);
            $response = $redirectResponse->generateHttpResponse($response);
        }

        return $response;
    }

}