<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    use EntityTrait;

    /**
     * @param string $username
     * @param string $password
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @return UserEntity|UserEntityInterface|null
     *
     * ToDo: This method is called to validate a user’s credentials.
     * - You can use the grant type to determine if the user is permitted to use the grant type.
     * - You can use the client entity to determine to if the user is permitted to use the client.
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $user = new UserEntity();
        return $user;
    }

}