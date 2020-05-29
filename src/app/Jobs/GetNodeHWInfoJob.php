<?php

namespace App\Jobs;

use App\Exceptions\PXEException;
use App\Exceptions\SSHException;
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
     */
    public function handle(PXEService $PXEService): void
    {
        $process = $this->getSSH()->execute('sudo ifconfig -a');
        $node = $this->getNode();
        if (!$process->isSuccessful()) {
            throw new SSHException(
                "SSH ERROR ({$node->ip}): {$process->getErrorOutput()}"
            );
        }

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
