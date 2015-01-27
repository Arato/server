<?php
use tests\helpers\Factory;

class AuthControllerTest extends ApiTester
{
    use Factory;

    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_log_in_user()
    {
        User::create([
            'email'          => 'testing@testing.com',
            'password'       => 'testing'
        ]);

        $credentials = [
            'email'    => 'testing@testing.com',
            'password' => 'testing'
        ];
        $this->getJson('login', 'POST', $credentials);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_throws_exception_if_login_fails()
    {
        User::create([
            'email'    => 'testing@testing.com',
            'password' => 'testing'
        ]);
        $this->getJson('login', 'POST', [
            'email'    => 'testing@testing.com',
            'password' => 'test'
        ]);
        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_should_log_out_user()
    {
        $user = User::create([
            'email'    => 'testing@testing.com',
            'password' => 'password'
        ]);
        $this->be($user);

        $this->getJson('logout', 'POST');
        $this->assertResponseStatus(204);
    }

    /**
     * Generate Alert mock
     * @return array
     */
    protected function getStub()
    {
        return [
        ];
    }
}