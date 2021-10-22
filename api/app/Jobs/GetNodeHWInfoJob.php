<?php

namespace App\Jobs;

use App\Exceptions\SSHException;
use App\Models\Node;
use App\Services\PXEService;
use Illuminate\Support\Collection;

class GetNodeHWInfoJob extends BaseSSHJob
{
    public const MODEL_UNKNOWN = 'unknown';

    private bool $dispatchStatusJob;

    /**
     * @param int $nodeId
     * @param bool $dispatchJobStatus
     */
    public function __construct(int $nodeId, bool $dispatchJobStatus = true)
    {
        parent::__construct($nodeId);

        $this->dispatchStatusJob = $dispatchJobStatus;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws SSHException
     * @throws \JsonException
     */
    public function handle(): void
    {
        $this->networkInfo();
        $this->hostnameInfo();
        $this->cpuInfo();
        $this->ramInfo();
        $this->modelInfo();
        $this->bootOrderInfo();
        $this->getBootloaderTimestamp();

        if ($this->dispatchStatusJob) {
            GetNodeStatusJob::dispatchSync($this->getNode()->id);
        }
    }

    /**
     * @throws SSHException
     */
    private function bootOrderInfo(): void
    {
        $node = $this->getNode();
        $output = $this->executeOrFail('sudo rpi-eeprom-config')->getOutput();

        // default to perfect order
        $order = '0x' . Node::BOOT_RESTART . Node::BOOT_SDCARD . Node::BOOT_USB . Node::BOOT_NETWORK;
        if (preg_match('/^BOOT_ORDER=(.*)$/m', $output, $matches)) {
            $order = $matches[1];
        }

        // remove the 0x and reverse the string
        $node->boot_order = Node::decodeBootOrder($order);
        $node->netbootable = \Str::startsWith($node->boot_order, Node::BOOT_NETWORK);
        $node->save();
    }

    /**
     * @throws SSHException
     */
    private function getBootloaderTimestamp(): void
    {
        $node = $this->getNode();

        if ($node->getVersion() < 4) {
            return;
        }

        $output = $this->executeOrFail('sudo vcgencmd bootloader_version')->getOutput();

        if (!preg_match('/^timestamp (.*)$/m', $output, $matches)) {
            $this->failWithMessage('Could not get the bootloader timestamp.');
        }

        $node->bootloader_timestamp = $matches[1];
        $node->save();
    }

    /**
     * @throws SSHException
     */
    private function modelInfo(): void
    {
        $node = $this->getNode();

        $process = $this->executeOrFail('cat /proc/cpuinfo');

        // we need to normalize the output
        $output = str_replace("\t", '', $process->getOutput());

        $model = static::MODEL_UNKNOWN;
        foreach (explode("\n", $output) as $row) {
            if (!empty($row)) {
                [$key, $value] = explode(': ', $row);

                if ($key === 'Model') {
                    $model = $value;
                    break;
                }
            }
        }

        $node->model = $model;
        $node->save();
    }

    /**
     * @throws SSHException
     */
    private function ramInfo(): void
    {
        $node = $this->getNode();
        $process = $this->executeOrFail('free --mega');

        $matches = [];
        preg_match("/(?:Mem:\s*)(?<memory>(?:[\S]+))/", $process->getOutput(), $matches);

        if (!isset($matches['memory'])) {
            $this->failWithMessage('Memory not found.');
        }

        $node->ram_max = $matches['memory'];
        $node->save();
    }

    /**
     * @throws SSHException
     * @throws \JsonException
     */
    private function cpuInfo(): void
    {
        $node = $this->getNode();
        $process = $this->executeOrFail('lscpu --json');
        $data = collect(
            json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR)['lscpu']
        )->keyBy('field')->map(fn($item) => $item['data']);

        // map information into db columns
        $fieldMap = [
            'Architecture:' => 'arch',
            'CPU(s):' => 'cpus',
            'CPU max MHz:' => 'cpu_max_freq',
        ];
        foreach ($fieldMap as $field => $column) {
            if (isset($data[$field])) {
                $node->{$column} = $data[$field];
            }
        }

        $node->save();
    }

    /**
     * @throws SSHException
     * @throws \JsonException
     */
    private function networkInfo(): void
    {
        $node = $this->getNode();

        $process = $this->executeOrFail('sudo ip --json addr');
        $data = collect(
            json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR)
        )->keyBy('ifname')
            ->forget('lo')
            ->first(function($interface) use ($node) {
                if (isset($interface['addr_info'])) {
                    $exists = collect($interface['addr_info'])
                        ->first(fn($address) => $address['local'] === $node->ip);

                    return $exists !== null;
                }

                return false;
            });

        $node->mac = strtolower($data['address']);
        $node->save();
    }

    /**
     * @throws SSHException
     */
    private function hostnameInfo(): void
    {
        $node = $this->getNode();
        $process = $this->executeOrFail('hostname');

        $node->hostname = trim($process->getOutput());
        $node->save();
    }

}
