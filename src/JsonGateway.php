<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use I4code\JaApi\FileGateway;

class JsonGateway extends FileGateway
{

    public function retrieveAll(): array
    {
        $items = $this->getDecoded();
        return $items;
    }

    public function persist(array $data)
    {
        // TODO: Implement persist() method.
    }

}