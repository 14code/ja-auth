<?php


namespace I4code\JaAuth\TestMocks;


trait RepositoryMockTrait
{
    protected $faker;

    protected $clients;
    protected $clientJsonFile;
    protected $uniqueClientId;
    protected $scopes;
    protected $scopeJsonFile;
    protected $uniqueScopeId;
    protected $users;
    protected $userJsonFile;
    protected $uniqueUser;


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

}