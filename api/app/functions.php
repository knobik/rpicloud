<?php

use Symfony\Component\Process\Process;

if (!function_exists('hostIp')) {
    /**
     * @return string
     */
    function hostIp(): string
    {
        // cache the ip
        if (!app()->bound('_hostIp')) {
            app()->singleton(
                '_hostIp',
                function () {
                    $process = Process::fromShellCommandline('ip route get 1 | sed -n \'s/^.*src \([0-9.]*\) .*$/\1/p\'');
                    $process->run();

                    return trim($process->getOutput());
                }
            );
        }

        return app('_hostIp');
    }
}

if (!function_exists('d')) {
    /**
     * @param  array  $vars
     * @return string
     */
    function d(...$vars)
    {
        dump(...$vars);
    }
}
