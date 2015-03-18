<?php


namespace Arato\Push;

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class PushService
{
    protected $client;

    function __construct()
    {
        $this->client = new Client(new Version1X(getenv("NODE_PUSH")));
    }

    function emit($channel, Array $data)
    {
        $this->client->initialize();
        $this->client->emit($channel, $data);
        $this->client->close();
    }
}