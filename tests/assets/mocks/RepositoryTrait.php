<?php


namespace I4code\JaAuth\TestMocks;


trait RepositoryTrait
{
    protected $clients;
    protected $file;


    public function createClientJsonRepository(): void
    {
        $this->file = 'tests/assets/data/clients.json';
        $this->clients = [
            (object) [
                'id' => uniqid()
            ]
        ];
        file_put_contents($this->file, json_encode($this->clients));
    }

}