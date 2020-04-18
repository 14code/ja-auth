<?php


namespace I4code\JaAuth;


use http\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class SessionCookieResponse
{
    protected $sessionId;
    protected $sessionName;

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     * @return SessionCookieResponse
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * @param mixed $sessionName
     * @return SessionCookieResponse
     */
    public function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response)
    {
        $cookie = urlencode($this->getSessionName()) . '=' . urlencode($this->getSessionId());

        $params = session_get_cookie_params();

        if ($params['lifetime']) {
            $expires = gmdate('D, d M Y H:i:s T', $this->time + $params['lifetime']);
            $cookie .= "; expires={$expires}; max-age={$params['lifetime']}";
        }

        if ($params['domain']) {
            $cookie .= "; domain={$params['domain']}";
        }

        if ($params['path']) {
            $cookie .= "; path={$params['path']}";
        }

        if ($params['secure']) {
            $cookie .= '; secure';
        }

        if ($params['httponly']) {
            $cookie .= '; httponly';
        }

        return $response->withAddedHeader('Set-Cookie', $cookie);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function extractSessionIdFromResponse(ResponseInterface $response)
    {
        $cookieHeaders = $response->getHeader('Set-Cookie');
        foreach ($cookieHeaders as $cookieRow) {
            $cookieParts = array_filter(array_map('trim', explode(';', $cookieRow)));
            if (empty($cookieParts) || !strpos($cookieParts[0], '=')) {
                throw new RuntimeException('Invalid cookie in server response.');
            }
            list($cookieName, $cookieValue) = explode('=', $cookieParts[0]);
            if ($cookieName == $this->getSessionName()) {
                return $cookieValue;
            }
        }
    }

}