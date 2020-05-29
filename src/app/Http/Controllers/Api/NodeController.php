<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\InitSSH;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\RegisterNodeRequest;
use App\Jobs\GetNodeHWInfoJob;
use App\Models\Node;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class NodeController extends ApiController
{
    /**
     * run
     * curl -sL http://192.168.1.2/api/node/provision | sudo bash -
     *
     * @return string
     */
    public function provision(): string
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
            file_get_contents(base_path('scripts/provision.sh'))
        );
    }

    /**
     * @param  RegisterNodeRequest  $request
     * @return string
     * @throws FileNotFoundException
     */
    public function register(RegisterNodeRequest $request): string
    {
        $node = Node::firstOrCreate(
            [
                'ip' => $request->get('ip', $_SERVER['REMOTE_ADDR']),
            ]
        );

        GetNodeHWInfoJob::dispatch($node->id)->delay(5);

        return \Storage::disk('local')->get(InitSSH::RSA_PUBLIC);
    }
}
