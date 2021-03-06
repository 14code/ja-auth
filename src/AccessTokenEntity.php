<?php
declare(strict_types=1);


namespace I4code\JaAuth;


use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessTokenEntity implements AccessTokenEntityInterface
{
    use EntityTrait;
    use AccessTokenTrait;
    use TokenEntityTrait;

    protected $client;

    public function __construct(ClientEntityInterface $client) {
        $this->client = $client;
    }

}