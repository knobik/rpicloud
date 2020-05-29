<?php

use Symfony\Component\Process\Process;

if (!function_exists('hostIp')) {
    /**
     * @return string
     */
    function hostIp(): string
    {
        $process = new Process(['hostname', '-I']);
        $process->run();

        $addresses = explode(' ', $process->getOutput());

        return trim($addresses[0]);
    }
}
