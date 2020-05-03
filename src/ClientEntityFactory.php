<?php
declare(strict_types=1);


namespace I4code\JaAuth;


class ClientEntityFactory extends AbstractFactory
{

    public function createFromObject(object $data)
    {
        $name = '';
        if (isset($data->name)) {
            $name = $data->name;
        }
        $redirectUri = null;
        if (isset($data->redirectUri)) {
            $redirectUri = $data->redirectUri;
        }

        $isConfidential = false;
        if (isset($data->isConfidential)) {
            $isConfidential = $data->isConfidential;
        }

        $client = new ClientEntity($name, $redirectUri, $isConfidential);
        $client->setIdentifier($data->id);
        return $client;
    }

}