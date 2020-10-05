<?php


namespace App\Websockets\Shell;

use App\Exceptions\SSHException;
use App\Models\ShellToken;
use Illuminate\Auth\AuthenticationException;
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

    private ?Shell $ssh;

    /**
     * Client constructor.
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->id = Str::random(8);
        $this->ssh = null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param $token
     * @throws AuthenticationException
     * @throws SSHException
     */
    public function createShell($token): void
    {
        $token = ShellToken::whereToken($token)->first();
        if (!$token) {
            throw new AuthenticationException();
        }

        $this->ssh = new Shell($token->node);
        if (!$this->ssh->connect()) {
            throw new SSHException("SSH ERROR ({$token->node->ip}): Cant connect to SSH.");
        }
    }

    /**
     * @return Shell|null
     */
    public function shell(): ?Shell
    {
        return $this->ssh;
    }

    /**
     * @return void
     */
    public function close(): void
    {
            $this->connection->close();
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        if ($this->shell()) {
            $this->shell()->disconnect();
            $this->ssh = null;
        }
    }

}
