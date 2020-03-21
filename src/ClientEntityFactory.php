<?php
declare(strict_types=1);


namespace I4code\JaAuth;


class ClientEntityFactory extends AbstractFactory
{

    public function createFromObject(object $data)
    {
        $client = new ClientEntity();
        $client->setIdentifier($data->id);
        return $client;
    }

}