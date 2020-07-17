<?php

namespace App\Jobs\Operations;

class SetHostnameJob extends BaseOperationJob
{
    public const MOUNT_POINT = '/mnt';

    /**
     * @var string
     */
    private string $hostname;

    /**
     * @var string
     */
    private string $device;

    public function __construct(int $nodeId, string $device, string $hostname)
    {
        parent::__construct($nodeId);

        $this->hostname = $hostname;
        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        $this->track("Setting hostname to {$this->hostname}");

        $hostnameFile = $this->makeTmpFile($this->fillParameters($this->getStub('hostname/hostname')));
        $hostsFile = $this->makeTmpFile($this->fillParameters($this->getStub('hostname/hosts')));

        // mount the root partition so we can put files inside
        $this->execute("sudo mount {$this->device}p2 " . static::MOUNT_POINT);

        $this->getSSH()->upload($hostnameFile, $hostnameFile);
        $this->execute("sudo mv {$hostnameFile} " . static::MOUNT_POINT . '/etc/hostname');

        $this->getSSH()->upload($hostsFile, $hostsFile);
        $this->execute("sudo mv {$hostsFile} " . static::MOUNT_POINT . '/etc/hosts');

        $this->execute('sudo umount ' . static::MOUNT_POINT);
    }

    /**
     * @param string $content
     * @return string
     */
    private function fillParameters(string $content): string
    {
        return str_replace(
            [
                '__config.hostname__'
            ],
            [
                $this->hostname
            ],
            $content
        );
    }
}
