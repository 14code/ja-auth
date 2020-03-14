<?php
declare(strict_types=1);


namespace I4code\JaAuth;


use League\OAuth2\Server\Entities\ClientEntityInterface;

class ClientEntity implements ClientEntityInterface
{

    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function getRedirectUri()
    {
        $uri = 'notempty';
        return $uri;
    }

    public function isConfidential()
    {
        // TODO: Implement isConfidential() method.
    }

}