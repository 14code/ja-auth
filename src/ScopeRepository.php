<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use I4code\JaApi\Repository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository extends Repository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier)
    {
        $scopes = $this->findAll();
        //error_log(print_r($scopes, true));
        foreach ($scopes as $scope) {
            if ($identifier == $scope->getIdentifier()) {
                return $scope;
            }
        }
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        $scopes = [];
        return $scopes;
    }

}