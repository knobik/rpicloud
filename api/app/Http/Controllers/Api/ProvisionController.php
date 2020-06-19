<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\InitSSH;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\RegisterNodeRequest;
use App\Jobs\GetNodeHWInfoJob;
use App\Models\Node;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ProvisionController extends ApiController
{
    /**
     * run
     * curl -sL http://[host]:8080/api/provision/script | sudo bash -
     *
     * @return string
     */
    public function script(): string
    {
        return str_replace(
            [
                '__PASSWORD__',
                '__URL__'
            ],
            [
                \Str::random(100),
                config('app.url')
            ],
            file_get_contents(base_path('stubs/scripts/provision.sh'))
        );
    }

    /**
     * @param RegisterNodeRequest $request
     * @return string
     * @throws FileNotFoundException
     */
    public function register(RegisterNodeRequest $request): string
    {
        $node = Node::firstOrCreate(
            [
                'ip' => $request->get('ip', $_SERVER['REMOTE_ADDR']),
                'online' => true,
            ]
        );

        GetNodeHWInfoJob::dispatch($node->id)->delay(5);

        return \Storage::disk('local')->get(InitSSH::RSA_PUBLIC);
    }
}
