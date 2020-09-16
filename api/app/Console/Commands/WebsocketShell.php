<?php

namespace App\Console\Commands;

use App\Websockets\ShellHandler;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

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
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ShellHandler($this->getOutput())
                )
            ),
            static::PORT
        );

        $server->run();
    }
}
