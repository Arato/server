<?php

use Faker\Factory as Faker;

abstract class ApiTester extends \TestCase
{
    protected $fake;

    protected $times = 1;

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Route::enableFilters();
        Session::start();
    }

    function __construct()
    {
        $this->fake = Faker::create();
    }


    protected function getJson($url, $method = 'GET', array $parameters = [])
    {
        return json_decode($this->call($method, $url, $parameters)->getContent());
    }

    protected function assertObjectHasAttributes($object, Array $attributes = [])
    {
        foreach ($attributes as $attr) {
            $this->assertObjectHasAttribute($attr, $object);
        }
    }
}