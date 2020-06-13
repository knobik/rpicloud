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
                    $process = new Process(['hostname', '-I']);
                    $process->run();

                    $addresses = explode(' ', $process->getOutput());

                    return trim($addresses[0]);
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
