<?php


namespace App\Websockets;


use App\Exceptions\SSHException;
use App\Websockets\Shell\Client;
use Illuminate\Auth\AuthenticationException;
use JsonException;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtLibevLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use SplObjectStorage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShellHandler
 * @package App\Websockets
 */
class ShellHandler implements MessageComponentInterface
{
    public const SSH_PULL_INTERVAL = 100;

    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var SplObjectStorage
     */
    private SplObjectStorage $connections;

    /**
     * @var ExtEventLoop|ExtEvLoop|ExtLibeventLoop|ExtLibevLoop|ExtUvLoop|LoopInterface|StreamSelectLoop
     */
    private $loop;

    /**
     * ShellHandler constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->connections = new SplObjectStorage();
        $this->loop = LoopFactory::create();

        $this->loop->addPeriodicTimer(static::SSH_PULL_INTERVAL / 1000, function ($timer) {
            foreach ($this->connections as $connection) {
                /** @var ConnectionInterface $connection */
                if ($data = optional($this->client($connection)->shell())->read()) {
                    $connection->send($data);
                }
            }
        });
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
        $this->client($conn)->cleanup();
        $this->connections->detach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $this->output->writeln('Error ['.$this->client($conn)->getId().']: ' . $e->getMessage());
        $this->client($conn)->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @param MessageInterface $msg
     * @throws JsonException
     * @throws AuthenticationException
     * @throws SSHException
     */
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg): void
    {
        try {
            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);

            // refactor to switch if more actions will be added
            if ($data['action'] === 'auth') {
                $this->client($conn)->createShell($data['data']['token']);
            }

        } catch (JsonException $exception) {
            if ($this->client($conn)->shell()) {
                $this->client($conn)->shell()->write($msg);
            }
        }

        // TODO: Implement onMessage() method.
    }

    /**
     * @return ExtEventLoop|ExtEvLoop|ExtLibeventLoop|ExtLibevLoop|ExtUvLoop|LoopInterface|StreamSelectLoop
     */
    public function getLoop()
    {
        return $this->loop;
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
