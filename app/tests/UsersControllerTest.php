<?php

use tests\helpers\Factory;

class UsersControllerTest extends ApiTester
{
    use Factory;

    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_fetches_users()
    {
        $this->make('User');

        $response = $this->get('api/v1/users');

        $this->assertResponseOk();
        $this->assertNotNull($response->users);
        $this->assertNotNull($response->paginate);
    }

    /** @test */
    public function it_fetches_a_single_user()
    {
        $this->make('User');

        $response = $this->get('api/v1/users/1');

        $this->assertResponseOk();
        $this->assertObjectHasAttributes($response->users, ['email']);
    }

    /** @test */
    public function it_404_if_a_user_is_not_found()
    {
        $response = $this->get('api/v1/users/2');
        $this->assertResponseStatus(404);
        $this->assertObjectHasAttributes($response, ['error']);
    }

    /** @test */
    public function it_creates_a_new_user_given_valid_parameters()
    {
        $this->createUserAndAuthenticate();

        $this->post('api/v1/users', $this->getStub());

        $this->assertResponseStatus(201);
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_a_new_user_request_fails_validation()
    {
        $this->createUserAndAuthenticate();

        $this->post('api/v1/users');

        $this->assertResponseStatus(400);
    }
    
    /** @test */
    public function it_updates_a_user_given_valid_parameters()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $response = $this->put('api/v1/users/1', [
            'email' => "newemail@email.com"
        ]);

        $this->assertResponseStatus(200);
        $this->assertEquals($response->users->email, "newemail@email.com");
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_an_updated_user_request_fails_validation()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->put('api/v1/users/1', [
            'password'              => "a",
            'password_confirmation' => 'aa'
        ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_throws_a_401_error_if_not_authenticated_for_update()
    {
        $this->make('User');
        $this->put('api/v1/users/1', [
            'email' => "newemail@email.com"
        ]);

        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_throws_a_403_error_if_a_user_is_not_authorized_to_update_user()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->put('api/v1/users/2', [
            'email' => "newemail@email.com"
        ]);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_throws_a_404_error_if_an_updated_user_does_not_exists()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->put('api/v1/users/3', [
            'email' => "newemail@email.com"
        ]);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_deletes_a_user()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->delete('api/v1/users/1');

        $this->assertResponseStatus(204);
    }

    /** @test */
    public function it_throws_a_403_error_if_a_user_is_not_authorized_to_delete_user()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->delete('api/v1/users/2');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_throws_a_404_error_if_a_deleted_user_does_not_exists()
    {
        $this->createUserAndAuthenticate();

        $this->make('User');
        $this->delete('api/v1/users/3');

        $this->assertResponseStatus(404);
    }

    /**
     * Generate User mock
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
