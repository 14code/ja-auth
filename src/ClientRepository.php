<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use I4code\JaApi\Factory;
use I4code\JaApi\Gateway;
use I4code\JaApi\Repository;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends Repository implements ClientRepositoryInterface
{

    public function __construct(Gateway $gateway, Factory $factory)
    {
        parent::__construct($gateway, $factory);
    }

    public function getClientEntity($clientIdentifier)
    {
        $client = new ClientEntity();
        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        // TODO: Implement validateClient() method.
    }

}