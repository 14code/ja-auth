<?php


namespace I4code\JaAuth\TestMocks;


trait RepositoryMockTrait
{
    protected $clients;
    protected $clientJsonFile;
    protected $uniqueClientId;
    protected $scopes;
    protected $scopeJsonFile;
    protected $uniqueScopeId;


    public function createClientJsonRepository(): void
    {
        $this->uniqueClientId = uniqid('client');
        $this->clientJsonFile = 'tests/assets/data/clients.json';
        $this->clients = [
            (object) [
                'id' => $this->uniqueClientId
            ],
            (object) [
                'id' => uniqid('client')
            ],
            (object) [
                'id' => uniqid('client')
            ]
        ];
        file_put_contents($this->clientJsonFile, json_encode($this->clients));
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