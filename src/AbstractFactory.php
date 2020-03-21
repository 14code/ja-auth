<?php
declare(strict_types=1);

namespace I4code\JaAuth;

use I4code\JaApi\Factory;

abstract class AbstractFactory implements Factory
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

    abstract public function createFromObject(object $data);

}