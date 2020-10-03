<?php


namespace App\Websockets;


use App\Websockets\Shell\Client;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShellHandler
 * @package App\Websockets
 */
class ShellHandler implements MessageComponentInterface
{
    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var SplObjectStorage
     */
    private SplObjectStorage $connections;

    /**
     * ShellHandler constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->connections = new SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        $this->connections->attach($conn, new Client($conn));
        $this->output->writeln('New connection. ID: ' . $this->client($conn)->getId());
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $this->output->writeln('Connection closed. ID: ' . $this->client($conn)->getId());
        $this->connections->detach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        dd($e);
    }

    /**
     * @param ConnectionInterface $conn
     * @param MessageInterface $msg
     * @throws \JsonException
     */
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg): void
    {
        $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);

        switch ($data['action']) {
            case 'auth':
                $this->client($conn)->shell($data['data']['token']);
                break;

            case 'data':

                break;
        }

        d(
            $data
        );

        // TODO: Implement onMessage() method.
    }

    /**
     * @param ConnectionInterface $conn
     * @return Client
     */
    private function client(ConnectionInterface $conn): Client
    {
        /** @var Client $client */
        $client = $this->connections[$conn];

        return $client;
    }
}
