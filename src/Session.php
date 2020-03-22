<?php
declare(strict_types=1);


namespace I4code\JaAuth;


use League\OAuth2\Server\Entities\UserEntityInterface;

class Session
{
    protected $user;

    /**
     * @return bool
     */
    public function userIsApproved(): bool
    {
        return $this->user instanceof UserEntityInterface;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface
    {
        if ($this->userIsApproved()) {
            return $this->user;
        }
        return new class implements UserEntityInterface {
            public function getIdentifier()
            {}
        };
    }

    /**
     * @param mixed $user
     * @return Session
     */
    public function setUser(?UserEntityInterface $user)
    {
        $this->user = $user;
        return $this;
    }

}