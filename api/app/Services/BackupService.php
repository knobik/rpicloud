<?php


namespace App\Services;


use App\Models\Node;
use App\Operations\BackupOperation;

class BackupService
{
    public function makeBackup(Node $node, string $device, string $filename, bool $shrink = true)
    {
        (new BackupOperation($node, $device, $filename, $shrink))->dispatch();
    }
}
