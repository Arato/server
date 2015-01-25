<?php
use tests\helpers\Factory;

class AlertsControllerTest extends ApiTester
{
    use Factory;

    public function setUp()
    {
        parent::setUp();

        $user = User::create(['email' => 'testing@testing.com', 'password' => 'password']);
        $this->be($user);
    }

    /** @test */
    public function it_fetches_alerts()
    {
        $this->make('Alert');

        $this->getJson('api/v1/alerts');

        $this->assertResponseOk();
    }

    /** @test */
    public function it_fetches_a_single_alert()
    {
        $this->make('Alert');
        $alert = $this->getJson('api/v1/alerts/1')->alerts;

        $this->assertResponseOk();
        $this->assertObjectHasAttributes($alert, ['title', 'price', 'content']);
    }

    /** @test */
    public function it_404_if_an_alert_is_not_found()
    {
        $alert = $this->getJson('api/v1/alerts/2');
        $this->assertResponseStatus(404);
        $this->assertObjectHasAttributes($alert, ['error']);
    }

    /** @test */
    public function it_creates_a_new_alert_given_valid_parameters()
    {
        $this->getJson('api/v1/alerts', 'POST', $this->getStub());

        $this->assertResponseStatus(201);
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_a_new_alert_request_fails_validation()
    {
        $this->getJson('api/v1/alerts', 'POST');

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_updates_an_alert_given_valid_parameters()
    {
        $this->make('Alert');
        $updatedAlert = $this->getJson('api/v1/alerts/1', 'PUT', [
            'title' => "my second title"
        ]);

        $this->assertResponseStatus(200);
        $this->assertEquals($updatedAlert->alerts->title, "my second title");
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_an_updated_alert_request_fails_validation()
    {
        $this->make('Alert');
        $this->getJson('api/v1/alerts/1', 'PUT', [
            'price' => -3
        ]);

        $this->assertResponseStatus(400);
    }

    /** @test */
    public function it_deletes_an_alert()
    {
        $this->make('Alert');
        $deletedAlert = $this->getJson('api/v1/alerts/1', 'DELETE');

        $this->assertResponseStatus(200);
        $this->assertEquals($deletedAlert->alerts, 'Alert successfully deleted.');
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