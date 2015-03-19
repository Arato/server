<?php


namespace Arato\Push;

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;
use Log;

class PushService
{
    protected $client;

    function __construct()
    {
        $this->client = new Client(new Version1X(getenv("NODE_PUSH")));
    }

    /**
     * @param       $channel
     * @param array $data
     */
    function emit($channel, Array $data)
    {
        try {
            $this->client->initialize();
            $this->client->emit($channel, $data);
            $this->client->close();
        }
        catch (\RuntimeException $e) {
            Log::info("catch", [$e]);
        }
    }
}