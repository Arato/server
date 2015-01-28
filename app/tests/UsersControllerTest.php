<?php

use tests\helpers\Factory;

class UsersControllerTest extends ApiTester
{
    use Factory;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->createUserAndAuthenticate();
    }

    /** @test */
    public function it_fetches_users()
    {
        $this->make('User');

        $this->getJson('api/v1/users');

        $this->assertResponseOk();
    }

    /** @test */
    public function it_fetches_a_single_user()
    {
        $user = $this->getJson('api/v1/users/1')->users;

        $this->assertResponseOk();
        $this->assertObjectHasAttributes($user, ['email']);
    }

    /** @test */
    public function it_404_if_a_user_is_not_found()
    {
        $user = $this->getJson('api/v1/users/2');
        $this->assertResponseStatus(404);
        $this->assertObjectHasAttributes($user, ['error']);
    }

    /** @test */
    public function it_creates_a_new_user_given_valid_parameters()
    {
        $this->getJson('api/v1/users', 'POST', $this->getStub());

        $this->assertResponseStatus(201);
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_a_new_user_request_fails_validation()
    {
        $this->getJson('api/v1/users', 'POST');

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_updates_a_user_given_valid_parameters()
    {
        $updatedAlert = $this->getJson('api/v1/users/' . $this->user['id'], 'PUT', [
            'email' => "newemail@email.com"
        ]);

        $this->assertResponseStatus(200);
        $this->assertEquals($updatedAlert->users->email, "newemail@email.com");
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_an_updated_user_request_fails_validation()
    {
        $this->getJson('api/v1/users/' . $this->user['id'], 'PUT', [
            'password'              => "a",
            'password_confirmation' => 'aa'
        ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_deletes_a_user()
    {
        $this->getJson('api/v1/users/' . $this->user['id'], 'DELETE');

        $this->assertResponseStatus(204);
    }

    /**
     * Generate Alert mock
     * @return array
     */
    protected function getStub()
    {
        return [
            'email'                 => $this->fake->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];
    }
}
