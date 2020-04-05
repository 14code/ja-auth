<?php


namespace I4code\JaAuth\TestMocks;


trait MockConfigTrait
{
    protected $keyDir = __DIR__ . '/../../../.keys';

    public function getEncryptionKey()
    {
        return file_get_contents($this->keyDir . '/encryption.key');
    }

    public function getPrivateKeyPath()
    {
        return $this->keyDir . '/private.key';
    }

    public function getPublicKeyPath()
    {
        return $this->keyDir . '/public.key';
    }

}