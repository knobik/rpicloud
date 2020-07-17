<?php

namespace App\Jobs\Operations;

use App\Console\Commands\InitSSH;
use App\Exceptions\SSHException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class AddSystemUserJob extends BaseOperationJob
{
    public const MOUNT_POINT = '/mnt';

    /**
     * @var string
     */
    private string $device;

    public function __construct(int $nodeId, string $device)
    {
        parent::__construct($nodeId);

        $this->device = $device;
    }

    /**
     * Execute the job.
     * @throws SSHException
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->track("Adding system user");
        $this->execute("sudo mount {$this->device}p2 " . static::MOUNT_POINT);

        // mount the root partition so we can put files inside
        $process = $this->execute('cat ' . static::MOUNT_POINT . '/etc/passwd | grep ' . config('pxe.user') . ':');

        if (empty(trim($process->getOutput()))) {
            foreach (static::commandChain(static::MOUNT_POINT) as $command) {
                $process = $this->execute($command);

                if (!$process->isSuccessful()) {
                    throw new SSHException(
                        'Error while running command: ' . $command . "\n\n" . $process->getErrorOutput()
                    );
                }
            }
        } else {
            $this->track("System user exists. Skipping...");
        }

        $this->execute('sudo umount ' . static::MOUNT_POINT);
    }

    /**
     * @param string $basePath
     * @return array
     * @throws FileNotFoundException
     */
    public static function commandChain(string $basePath = ''): array
    {
        $password = bcrypt(Str::random(32));
        $authKey = \Storage::disk('local')->get(InitSSH::RSA_PUBLIC);
        $user = config('pxe.user');

        return [
            // add a user
            "echo '{$user}:x:1337:1337:,,,:/home/{$user}:/bin/bash' | sudo tee -a {$basePath}/etc/passwd",
            "echo '{$user}:x:1337:' | sudo tee -a {$basePath}/etc/group",
            "echo '{$user}:{$password}:18409:0:99999:7:::' | sudo tee -a {$basePath}/etc/shadow",

            // enable passwordless login
            "sudo mkdir -p {$basePath}/home/{$user}/.ssh",
            "echo '{$authKey}' | sudo tee -a {$basePath}/home/{$user}/.ssh/authorized_keys",
            "sudo chown -R 1337:1337 {$basePath}/home/{$user}",

            // add user to sudo
            "sudo sed -i 's/sudo:x:27:pi/sudo:x:27:pi,{$user}/g' {$basePath}/etc/group",
            "echo '{$user} ALL=(ALL) NOPASSWD:ALL' | sudo tee -a {$basePath}/etc/sudoers",
        ];
    }
}
