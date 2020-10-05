<?php

namespace App\Console\Commands;

use App\Websockets\ShellHandler;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server as Reactor;

class WebsocketShell extends Command
{
    public const PORT = 8081;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:shell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a websocket -> shell proxy server.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting shell proxy server at port ' . static::PORT . '...');
        $handler = new ShellHandler($this->getOutput());

        $socket = new Reactor('0.0.0.0' . ':' . static::PORT, $handler->getLoop());
        $server = new IoServer(new HttpServer(new WsServer($handler)), $socket, $handler->getLoop());

        $server->run();
    }
}
