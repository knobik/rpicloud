<?php

namespace App\Jobs\Operations;

use App\Models\Node;

class SetBootOrderJob extends BaseOperationJob
{

    /**
     * @var array
     */
    private array $bootOrder;

    /**
     * @var bool
     */
    private bool $includeDhcpOption;

    public function __construct(int $nodeId, array $bootOrder, bool $includeDhcpOption)
    {
        parent::__construct($nodeId);

        $this->bootOrder = $bootOrder;
        $this->includeDhcpOption = $includeDhcpOption;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track("Setting boot order.");

        $bootConf = $this->executeOrFail('sudo rpi-eeprom-config')->getOutput();

        if ($this->includeDhcpOption) {
            $bootConf = $this->insertOrReplace($bootConf, 'DHCP_TIMEOUT', '5000');
            $bootConf = $this->insertOrReplace($bootConf, 'DHCP_REQ_TIMEOUT', '500');
        }

        $bootConf = $this->insertOrReplace($bootConf, 'BOOT_ORDER', Node::encodeBootOrder($this->bootOrder));
        $tmpFile = $this->makeTmpFile($bootConf, 'boot.conf');
        $this->getSSH()->upload($tmpFile, $tmpFile);

        $this->executeOrFail("sudo rpi-eeprom-config --apply {$tmpFile}");
    }

    /**
     * @param string $content
     * @param string $key
     * @param string $value
     * @return string
     */
    private function insertOrReplace(string $content, string $key, string $value): string
    {
        $replacement = "{$key}={$value}";
        if (str_contains($content, $key)) {
            $content = \preg_replace(
                "/^{$key}=(.*)$/m",
                $replacement,
                $content
            );
        } else {
            $content .= "{$replacement}\n";
        }

        return $content;
    }
}
