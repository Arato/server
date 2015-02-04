<?php

use tests\helpers\Factory;

class AlertsControllerTest extends ApiTester
{
    use Factory;

    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_fetches_alerts()
    {
        $this->make('Alert');

        $response = $this->get('api/v1/alerts');

        $this->assertResponseOk();
        $this->assertNotNull($response->alerts);
        $this->assertNotNull($response->paginate);
    }

    /** @test */
    public function it_fetches_alerts_with_filter()
    {
        $this->times(4)->make('Alert', [
            'price' => 12
        ]);
        $this->times(10)->make('Alert', [
            'price' => 15
        ]);
        $this->times(5)->make('Alert', [
            'price' => 110
        ]);

        $response = $this->get('api/v1/alerts?limit=10&priceMin=10&priceMax=20');

        $this->assertResponseOk();
        $this->assertEquals(14, $response->paginate->total_count);
    }

    /** @test */
    public function it_fetches_alerts_for_a_specific_user()
    {
        $this->times(1)->make('Alert', ['user_id' => 1]);
        $this->times(1)->make('Alert', ['user_id' => 2]);

        $this->createUserAndAuthenticate();

        $response = $this->get('api/v1/users/1/alerts');

        $this->assertResponseOk();
        $this->assertEquals(1, count($response->alerts));
    }

    /** @test */
    public function it_fetches_a_single_alert()
    {
        $this->make('Alert');
        $response = $this->get('api/v1/alerts/1');

        $this->assertResponseOk();
        $this->assertObjectHasAttributes($response->alerts, ['title', 'price', 'content']);
    }

    /** @test */
    public function it_404_if_an_alert_is_not_found()
    {
        $response = $this->get('api/v1/alerts/2');
        $this->assertResponseStatus(404);
        $this->assertObjectHasAttributes($response, ['error']);
    }

    /** @test */
    public function it_creates_a_new_alert_given_valid_parameters()
    {
        $this->createUserAndAuthenticate();

        $this->post('api/v1/alerts', $this->getStub());

        $this->assertResponseStatus(201);
    }


    /** @test */
    public function it_throw_a_bad_request_error_if_a_new_alert_request_fails_validation()
    {
        $this->createUserAndAuthenticate();

        $this->post('api/v1/alerts');

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_throws_a_401_error_if_not_authenticated_for_creation()
    {
        $this->post('api/v1/alerts');

        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_updates_an_alert_given_valid_parameters()
    {
        $this->createUserAndAuthenticate();

        $this->make('Alert');
        $response = $this->put('api/v1/alerts/1', [
            'title' => "my second title",
            'price' => 10
        ]);

        $this->assertResponseStatus(200);
        $this->assertEquals($response->alerts->title, "my second title");
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_an_updated_alert_request_fails_validation()
    {
        $this->createUserAndAuthenticate();

        $this->make('Alert');
        $this->put('api/v1/alerts/1', [
            'price' => -3
        ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_throws_a_401_error_if_not_authenticated_for_update()
    {
        $this->make('Alert');
        $this->put('api/v1/alerts/1', [
            'title' => "my second title",
            'price' => 10
        ]);

        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_throws_a_403_error_if_a_user_is_not_authorized_to_update_alert()
    {
        $this->createUserAndAuthenticate();
        $this->make('Alert', ['user_id' => 2]);
        $this->put('api/v1/alerts/1', [
            'title' => "my second title",
        ]);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_throws_a_404_error_if_an_updated_alert_does_not_exists()
    {
        $this->createUserAndAuthenticate();

        $this->make('Alert');
        $this->put('api/v1/alerts/2', [
            'title' => "my second title",
            'price' => 10
        ]);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_deletes_an_alert()
    {
        $this->createUserAndAuthenticate();

        $this->make('Alert');
        $this->delete('api/v1/alerts/1');

        $this->assertResponseStatus(204);
    }

    /**
     * Generate Alert mock
     * @return array
     */
    protected function getStub()
    {
        return [
            'title'   => $this->fake->sentence(),
            'price'   => $this->fake->randomDigitNotNull,
            'content' => $this->fake->paragraph(),
            'user_id' => 1
        ];
    }
}