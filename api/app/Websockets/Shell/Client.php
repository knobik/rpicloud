<?php


namespace App\Websockets\Shell;

use Illuminate\Support\Str;
use Ratchet\ConnectionInterface;

class Client
{
    /**
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;

    /**
     * @var string
     */
    private string $id;

    /**
     * Client constructor.
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->id = Str::random(8);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function shell($token)
    {

    }
}
