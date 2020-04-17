<?php


namespace I4code\JaAuth;


use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class LoginServerException extends OAuthServerException
{

    /**
     * Invalid request method error.
     *
     * @param string $requiredMethod
     * @param ServerRequestInterface $serverRequest
     *
     * @return static
     */
    public static function invalidRequestMethod($requiredMethod, ServerRequestInterface $serverRequest)
    {
        $errorMessage = 'Invalid request method ' . $serverRequest->getMethod() . ', should be ' . $requiredMethod . '.';
        $exception = new static($errorMessage, 4, 'invalid_method', 400);

        $exception->setServerRequest($serverRequest);

        return $exception;
    }

}