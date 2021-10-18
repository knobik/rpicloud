<?php

namespace App\Console\Commands\traits;

use Symfony\Component\Process\Process;

class ProcessOutput
{
    private string $stdout = '';
    private string $stderr = '';
    private Process $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function addOutput(string $stdout): void
    {
        $this->stdout .= $stdout;
    }

    public function addErrorOutput(string $stderr): void
    {
        $this->stderr .= $stderr;
    }

    public function getErrorOutput(): string
    {
        return $this->stderr;
    }

    public function getOutput(): string
    {
        return $this->stdout;
    }

    public function isSuccessful(): bool
    {
        return $this->process->isSuccessful();
    }

    public function getProcess(): Process
    {
        return $this->process;
    }
}
