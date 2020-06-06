<?php

namespace App\Console\Commands;

use App\Exceptions\PXEException;
use App\Services\PXEService;
use Illuminate\Console\Command;
use phpseclib\Crypt\RSA;
use Symfony\Component\Process\Process;

class InitHost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:host';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the machine IP and fills it into the .env file.';

    /**
     * Execute the console command.
     *
     * @param  PXEService  $PXEService
     * @return mixed
     * @throws PXEException
     */
    public function handle(PXEService $PXEService)
    {
        $ip = hostIp();

        $this->writeNewEnvironmentFileWith('http://'.$ip.':8080');
        $this->setPxeDnsHost($PXEService, $ip);
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $url
     * @return void
     */
    protected function writeNewEnvironmentFileWith($url): void
    {
        file_put_contents(
            $this->laravel->environmentFilePath(),
            preg_replace(
                $this->keyReplacementPattern(),
                'APP_URL='.$url,
                file_get_contents($this->laravel->environmentFilePath())
            )
        );
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern(): string
    {
        $escaped = preg_quote('='.$this->laravel['config']['app.url'], '/');

        return "/^APP_URL{$escaped}/m";
    }

    /**
     * @param  PXEService  $PXEService
     * @param  string  $ip
     * @throws PXEException
     */
    private function setPxeDnsHost(PXEService $PXEService, string $ip): void
    {
        $process = Process::fromShellCommandline(
            'echo "dhcp-range='.$ip.',proxy" | sudo tee /etc/dnsmasq.d/dns-range.conf'
        );
        $process->run();

        $PXEService->restartPxeService();
    }
}
