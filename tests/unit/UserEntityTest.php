<?php


use I4code\JaAuth\UserEntity;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    use \I4code\JaAuth\TestMocks\RepositoryMockTrait;

    protected $user;

    public function setUp(): void
    {
        $this->user = $this->createUserDataObject();
    }

    public function testConstructor()
    {
        $user = new UserEntity($this->user->email, $this->user->password);
        $this->assertInstanceOf(\League\OAuth2\Server\Entities\UserEntityInterface::class, $user);
    }

    public function testGetLogin()
    {
        $user = new UserEntity($this->user->email, $this->user->password);
        $user->setLogin($this->user->login);
        $this->assertEquals($this->user->login, $user->getLogin());
    }

    public function testGetLoginEmailOnly()
    {
        $user = new UserEntity($this->user->email, $this->user->password);
        $this->assertEquals($this->user->email, $user->getLogin());
    }

    public function testGetLoginAvoidEmpty()
    {
        $user = new UserEntity($this->user->email, $this->user->password);
        $user->setLogin('');
        $this->assertEquals($this->user->email, $user->getLogin());
    }

    public function testGetPassword()
    {
        $user = new UserEntity($this->user->email, $this->user->password);
        $this->assertEquals($this->user->password, $user->getPassword());
    }

}
