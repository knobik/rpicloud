<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib\Crypt\RSA;

class InitSSH extends Command
{
    public const RSA_PRIVATE = 'ssh/id_rsa';
    public const RSA_PUBLIC = 'ssh/id_rsa.pub';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:ssh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates ssh keys for remote management.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $storage = \Storage::disk('local');

        if (!$storage->exists(static::RSA_PRIVATE)) {
            $rsa = new RSA();
            $rsa->setPrivateKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
            $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
            $keys = $rsa->createKey();

            $storage->put(static::RSA_PRIVATE, $keys['privatekey']);
            $storage->put(static::RSA_PUBLIC, $keys['publickey']);

            chmod(storage_path('app/' . static::RSA_PRIVATE), 0600);

            $this->info('SSH keys generated.');
        }
    }
}
