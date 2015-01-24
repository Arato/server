<?php
use tests\helpers\Factory;
use tests\helpers\ApiTester;

class AlertsControllerFunctionalTest extends ApiTester
{
    use Factory;

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
        $alert = $this->getJson('api/v1/alerts/1')->data;

        $this->assertResponseOk();
        $this->assertObjectHasAttributes($alert, ['title', 'price'], 'content');
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
        $this->getJson('api/v1/alert', 'POST', $this->getStub());

        $this->assertResponseStatus(201);
    }

    /** @test */
    public function it_throw_a_bad_request_error_if_a_new_alert_request_fails_validation()
    {
        $this->getJson('api/v1/alerts', 'POST');

        $this->assertResponseStatus(400);
    }

    /**
     * Generate Alert mock
     * @return array
     */
    protected function getStub()
    {
        return [
            'title'   => $this->fake->sentence(),
            'price'   => $this->fake->numberBetween(0, 100),
            'content' => $this->fake->paragraph()
        ];
    }
}