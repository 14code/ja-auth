<?php
declare(strict_types=1);


namespace I4code\JaAuth;


use I4code\JaApi\Factory;

class ScopeEntityFactory implements Factory
{

    public function createFromArray(array $data)
    {
        return $this->createFromObject((object) $data);
    }

    public function create($data = null)
    {
        if (is_array($data)) {
            return $this->createFromArray($data);
        }
        if (is_object($data)) {
            return $this->createFromObject($data);
        }
    }

    public function createFromObject(object $data)
    {
        $client = new ScopeEntity();
        $client->setIdentifier($data->id);
        return $client;
    }

}