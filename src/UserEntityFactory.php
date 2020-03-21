<?php
declare(strict_types=1);


namespace I4code\JaAuth;


class UserEntityFactory extends AbstractFactory
{

    public function createFromObject(object $data)
    {
        $user = new UserEntity($data->email, $data->password);
        $user->setIdentifier($data->id);
        $user->setLogin($data->login);
        $user->setName($data->name);
        return $user;
    }

}