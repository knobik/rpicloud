<?php

namespace App\Jobs\Operations;

use App\Jobs\BaseSSHJob;
use App\Jobs\Traits\TrackStatus;
use Illuminate\Support\Str;

abstract class BaseOperationJob extends BaseSSHJob
{
    use TrackStatus;

    /**
     * Shutdown the node
     *
     * @return void
     */
    protected function shutdown(): void
    {
        // if the node is online, try shutdown
        $this->execute('sudo shutdown now');

        $node = $this->getNode();
        $node->online = false;
        $node->save();
    }

    /**
     * Reboot the node
     *
     * @param int|null $sleepTime
     * @return void
     */
    protected function reboot(?int $sleepTime = null): void
    {
        // if the node is online, try to reboot it.
        $this->execute('sudo reboot');

        $node = $this->getNode();
        $node->online = false;
        $node->save();

        if ($sleepTime) {
            sleep($sleepTime);
        }
    }

    /**
     * @param string|null $hostname
     * @return void
     */
    protected function waitForBoot(?string $hostname = null): void
    {
        while (true) {
            sleep(5);

            $process = $this->execute('hostname');
            if ($process->isSuccessful()) {

                if ($hostname !== null) {
                    if (trim($process->getOutput()) === $hostname) {
                        break;
                    }
                } else {
                    break;
                }
            }
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getStub(string $filename): string
    {
        return file_get_contents(base_path("stubs/{$filename}"));
    }

    /**
     * @param string $contents
     * @return string
     */
    protected function makeTmpFile(string $contents)
    {
        $tmpFilename = '/tmp/' . Str::random(16);
        file_put_contents($tmpFilename, $contents);

        return $tmpFilename;
    }

}
