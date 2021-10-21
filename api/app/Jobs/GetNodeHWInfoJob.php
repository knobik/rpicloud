<?php

namespace App\Jobs;

use App\Exceptions\SSHException;
use App\Models\Node;
use App\Services\PXEService;
use Illuminate\Support\Collection;

class GetNodeHWInfoJob extends BaseSSHJob
{
    public const MODEL_UNKNOWN = 'unknown';

    /**
     * Execute the job.
     *
     * @param  PXEService  $PXEService
     * @return void
     * @throws SSHException
     * @throws \JsonException
     */
    public function handle(PXEService $PXEService): void
    {
        $this->networkInfo($PXEService);
        $this->hostnameInfo();
        $this->cpuInfo();
        $this->ramInfo();
        $this->modelInfo();
        $this->bootOrderInfo();

        GetNodeStatusJob::dispatchSync($this->getNode()->id);
    }

    /**
     * @throws SSHException
     */
    private function bootOrderInfo(): void
    {
        $node = $this->getNode();
        $output = $this->executeOrFail('sudo rpi-eeprom-config')->getOutput();

        if (!preg_match('/^BOOT_ORDER=(.*)$/m', $output, $matches)) {
            $this->failWithMessage('Could not get the boot order.');
        }

        // remove the 0x and reverse the string
        $node->boot_order = Node::decodeBootOrder($matches[1]);
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
     * @param  PXEService  $PXEService
     * @throws SSHException
     */
    private function networkInfo(PXEService $PXEService): void
    {
        $node = $this->getNode();
        $process = $this->executeOrFail('sudo ifconfig -a');

        $interface = $this->parseIpconfig($process->getOutput())
            ->first(fn($row) => $row['ip'] === $node->ip);

        if (!$interface || !isset($interface['mac'])) {
            $this->failWithMessage('Interface not found.');
        }

        $node->mac = strtolower($interface['mac']);
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

    /**
     * @return string
     */
    private function getIpConfigDelimiter(): string
    {
        return "\n\n";
    }

    /**
     * @return string
     */
    private function getIpConfigRegex(): string
    {
        return "^(?<interface>(?:[^\s:]+)).*?(?:inet (?<ip>(?:\d+\.?){4})|$).*?(?:ether (?<mac>(?:[^\s]+)))";
    }

    /**
     * @param  mixed  $input  ifconfig input to parse
     * @return Collection
     */
    private function parseIpconfig($input): Collection
    {
        $adapters = preg_split("/".$this->getIpConfigDelimiter()."/s", $input, null);
        $vals = [];
        foreach ($adapters as $int) {
            preg_match("/".$this->getIpConfigRegex()."/s", $int, $output);
            $vals[] = $output;
        }

        return $this->format($vals);
    }

    /**
     * @param  array  $vals  Array of extracted values
     * @return Collection
     */
    private function format(array $vals): Collection
    {
        $results = [];

        $expectedFields = [
            "interface",
            "ip",
            "mac",
        ];

        foreach ($vals as $v) {
            $new = [];
            foreach ($expectedFields as $field) {
                if (isset($v[$field])) {
                    $new[$field] = $v[$field];
                }
            }

            $results[] = $new;
        }

        return collect($results)->filter(fn($row) => isset($row['interface']));
    }
}
