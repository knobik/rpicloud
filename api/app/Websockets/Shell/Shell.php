<?php


namespace App\Websockets\Shell;


use App\Exceptions\SSHException;
use App\Models\Node;

class Shell
{
    public const COLS = 80;
    public const ROWS = 24;

    /**
     * @var resource
     */
    private $connection;

    /**
     * @var Node
     */
    private Node $node;
    /**
     * @var resource
     */
    private $shell;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return bool
     */
    public function connect(): bool
    {
        $this->connection = ssh2_connect($this->node->ip, $this->getSshPort(), ['hostkey' => 'ssh-rsa']);
        $auth = ssh2_auth_pubkey_file(
            $this->connection,
            config('pxe.user'),
            storage_path('app/ssh/id_rsa.pub'),
            storage_path('app/ssh/id_rsa')
        );

        if (!$this->connection || !$auth) {
            return false;
        }

        $this->shell = ssh2_shell($this->connection, 'xterm', null, self::COLS, self::ROWS, SSH2_TERM_UNIT_CHARS);

        return true;
    }

    /**
     * @return void
     */
    public function disconnect(): void
    {
        ssh2_disconnect($this->connection);
        $this->connection = null;
        $this->shell = null;
    }

    /**
     * @return string|null
     */
    public function read(): ?string
    {
        $lines = '';
        while ($line = fgets($this->shell)) {
            $lines .= $line;
        }

        return !empty($lines) ? mb_convert_encoding($lines, "UTF-8") : null;
    }

    /**
     * @param string $data
     */
    public function write(string $data): void
    {
        fwrite($this->shell, $data);
    }

    /**
     * @return int
     */
    private function getSshPort(): int
    {
        return 22;
    }
}
