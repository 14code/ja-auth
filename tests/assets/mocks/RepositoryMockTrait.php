<?php


namespace I4code\JaAuth\TestMocks;


trait RepositoryMockTrait
{
    protected $clients;
    protected $file;
    protected $scopes;
    protected $scopeJsonFile;
    protected $uniqueScopeId;


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

    public function createScopeJsonRepository(): void
    {
        $this->uniqueScopeId = 'valid';
        $this->scopeJsonFile = 'tests/assets/data/scopes.json';
        $this->scopes = [
            (object) [
                'id' => $this->uniqueScopeId
            ],
            (object) [
                'id' => 'user'
            ],
            (object) [
                'id' => 'archive'
            ]
        ];
        file_put_contents($this->scopeJsonFile, json_encode($this->scopes));
    }

}