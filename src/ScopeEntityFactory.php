<?php
declare(strict_types=1);


namespace I4code\JaAuth;


class ScopeEntityFactory extends AbstractFactory
{

    public function createFromObject(object $data)
    {
        $client = new ScopeEntity();
        $client->setIdentifier($data->id);
        return $client;
    }

}