<?php

namespace App\Jobs;

use App\Exceptions\PXEException;
use App\Exceptions\SSHException;
use App\Models\Node;
use App\Services\PXEService;
use Illuminate\Support\Collection;

class GetNodeHWInfoJob extends BaseSSHJob
{
    /**
     * Execute the job.
     *
     * @param  PXEService  $PXEService
     * @return void
     * @throws SSHException
     * @throws PXEException
     * @throws \JsonException
     */
    public function handle(PXEService $PXEService): void
    {
        $this->networkInfo($PXEService);
        $this->cpuInfo();
        $this->ramInfo();
    }

    /**
     * @throws SSHException
     */
    private function ramInfo(): void
    {
        $node = $this->getNode();
        $process = $this->command('free --mega');

        $matches = [];
        preg_match("/(?:Mem:\s*)(?<memory>(?:[\S]+))/", $process->getOutput(), $matches);

        if (!isset($matches['memory'])) {
            throw new SSHException(
                "SSH ERROR ({$node->ip}): memory not found."
            );
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
        $process = $this->command('lscpu --json');
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
     * @throws PXEException
     * @throws SSHException
     */
    private function networkInfo(PXEService $PXEService): void
    {
        $node = $this->getNode();
        $process = $this->command('sudo ifconfig -a');

        $interface = $this->parse($process->getOutput())
            ->first(fn($row) => $row['ip'] === $node->ip);

        if (!$interface || !isset($interface['mac'])) {
            throw new SSHException(
                "SSH ERROR ({$node->ip}): interface not found."
            );
        }

        $node->mac = strtolower($interface['mac']);
        $node->save();

        $PXEService->disableNetboot($node);
    }

    /**
     * @return string
     */
    private function getDelimiter(): string
    {
        return "\n\n";
    }

    /**
     * @return string
     */
    private function getRegex(): string
    {
        return "^(?<interface>(?:[^\s:]+)).*?(?:inet (?<ip>(?:\d+\.?){4})|$).*?(?:ether (?<mac>(?:[^\s]+)))";
    }

    /**
     * @param  mixed  $input  ifconfig input to parse
     * @return Collection
     */
    private function parse($input): Collection
    {
        $adapters = preg_split("/".$this->getDelimiter()."/s", $input, null);
        $vals = [];
        foreach ($adapters as $int) {
            preg_match("/".$this->getRegex()."/s", $int, $output);
            $vals[] = $output;
        }

        return $this->format($vals);
    }

    /**
     * @param  array  $vals  Array of extracted values
     * @return Collection
     */
    private function format($vals): Collection
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
