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
     * @return ProcessOutput
     */
    protected function processFromCommandline(string $commandLine, ?int $timeout = 60): ProcessOutput
    {
        $process = Process::fromShellCommandline($commandLine);
        $process->setTimeout($timeout);

        return $this->executeProcess($process);
    }

    /**
     * @param array $command
     * @param int|null $timeout
     * @return ProcessOutput
     */
    protected function process(array $command, ?int $timeout = 60): ProcessOutput
    {
        $process = new Process($command);
        $process->setTimeout($timeout);

        return $this->executeProcess($process);
    }

    /**
     * @param Process $process
     * @return ProcessOutput
     */
    protected function executeProcess(Process $process): ProcessOutput
    {
        $output = new ProcessOutput($process);
        $process->run(function ($type, $buffer) use ($output) {

            if (Process::OUT === $type) {
                $output->addOutput($buffer);

                if (config('app.debug')) {
                    $this->line($buffer);
                }
            } else {
                $output->addErrorOutput($buffer);

                if (config('app.debug')) {
                    $this->error($buffer);
                }
            }

        });

        return $output;
    }
}
