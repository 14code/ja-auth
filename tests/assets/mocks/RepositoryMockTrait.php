<?php


namespace I4code\JaAuth\TestMocks;


trait RepositoryMockTrait
{
    protected $faker;

    protected $clients;
    protected $clientJsonFile;
    protected $uniqueClientId;
    protected $confidentialClientId;
    protected $scopes;
    protected $scopeJsonFile;
    protected $uniqueScopeId;
    protected $users;
    protected $userJsonFile;
    protected $uniqueUser;
    protected $localSessionFile;
    protected $localSession;


    public function getFaker()
    {
        if (null == $this->faker) {
            $this->faker = \Faker\Factory::create('de_DE');
        }
        return $this->faker;
    }


    public function createClientJsonRepository(): void
    {
        $this->uniqueClientId = uniqid('client');
        $this->confidentialClientId = uniqid('client');
        $this->clientJsonFile = 'tests/assets/data/clients.json';
        $client = $this->createClientDataObject();
        $client->id = $this->uniqueClientId;
        $this->clients[] = $client;
        $client = $this->createClientDataObject();
        $this->clients[] = $client;
        $client = $this->createClientDataObject();
        $client->id = $this->confidentialClientId;
        $client->isConfidential = true;
        $this->clients[] = $client;
        file_put_contents($this->clientJsonFile, json_encode($this->clients));
    }


    public function createClientDataObject(): object
    {
        $faker = $this->getFaker();
        return (object) [
            'id' => uniqid('client'),
            'name' => $faker->domainName
        ];
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


    public function createUserJsonRepository(): void
    {
        $this->userJsonFile = 'tests/assets/data/users.json';
        $this->uniqueUser = $this->createUserDataObject();
        $this->users[] = $this->createUserDataObject();
        $this->users[] = $this->uniqueUser;
        $this->users[] = $this->createUserDataObject();
        file_put_contents($this->userJsonFile, json_encode($this->users));
    }


    public function createUserDataObject(): object
    {
        $faker = $this->getFaker();
        return (object) [
            'id' => uniqid('user'),
            'name' => $faker->name(),
            'email' => $faker->email,
            'login' => $faker->userName,
            'password' => $faker->password
        ];
    }

    public function storeLocalSession($sessionData)
    {
        $this->localSessionFile = 'tests/assets/data/localSession.json';
        file_put_contents($this->localSessionFile, json_encode($sessionData));
    }

    public function loadLocalSession()
    {
        $this->localSessionFile = 'tests/assets/data/localSession.json';
        $this->localSession = json_decode(file_get_contents($this->localSessionFile));
    }

}