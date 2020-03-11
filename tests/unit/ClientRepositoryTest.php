<?php

use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{

    public function testConstruct()
    {
        $repository = new \I4code\JaAuth\ClientRepository();
        $this->assertInstanceOf(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class, $repository);
    }

}
