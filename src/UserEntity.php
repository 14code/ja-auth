<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * ToDo: Email validation
 * - format
 * - verification link
 */
class UserEntity implements UserEntityInterface
{
    use EntityTrait;

    protected $login;
    protected $password;
    protected $email;
    protected $name;

    /**
     * UserEntity constructor.
     * @param $password
     * @param $email
     */
    public function __construct($email, $password, $login = '')
    {
        $this->email = $email;
        $this->password = $password;

        if (empty($login)) {
            $this->login = $email;
        } else {
            $this->login = $login;
        }
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        if (empty($this->login)) {
            return $this->getEmail();
        }
        return $this->login;
    }

    /**
     * @param mixed $login
     * @return UserEntity
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return UserEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return UserEntity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}