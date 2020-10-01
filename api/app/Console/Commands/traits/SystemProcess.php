<?php


namespace App\Console\Commands\traits;


use Symfony\Component\Process\Process;

/**
 * Trait SystemProcess
 * @package App\Console\Commands\traits
 */
trait SystemProcess
{

    /**
     * @param string $commandLine
     * @param int|null $timeout
     */
    protected function processFromCommandline(string $commandLine, ?int $timeout = 60): void
    {
        $process = Process::fromShellCommandline($commandLine);
        $process->setTimeout($timeout);

        $this->executeProcess($process);
    }

    /**
     * @param array $command
     * @param int|null $timeout
     */
    protected function process(array $command, ?int $timeout = 60): void
    {
        $process = new Process($command);
        $process->setTimeout($timeout);

        $this->executeProcess($process);
    }

    /**
     * @param Process $process
     * @return void
     */
    protected function executeProcess(Process $process): void
    {
        if (config('app.debug')) {
            $process->start();
            foreach ($process as $type => $data) {
                if ($process::OUT === $type) {
                    $this->line($data);
                } else {
                    $this->error($data);
                }
            }
        } else {
            $process->run();
        }
    }
}
