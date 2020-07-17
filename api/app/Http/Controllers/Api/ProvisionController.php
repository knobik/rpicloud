<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\InitSSH;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\RegisterNodeRequest;
use App\Jobs\GetNodeHWInfoJob;
use App\Models\Node;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;

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
        return $this->fillParameters(file_get_contents(base_path('stubs/scripts/provision.sh')));
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
            ]
        );

        // we know its online
        $node->online = true;
        $node->save();

        GetNodeHWInfoJob::dispatch($node->id)->delay(5);

        return \Storage::disk('local')->get(InitSSH::RSA_PUBLIC);
    }

    /**
     * @param string $content
     * @return string
     */
    private function fillParameters(string $content): string
    {
        $config = Arr::dot(config('pxe'));
        $keys = collect(array_keys($config))->map(fn($value) => "__config.{$value}__")->all();
        $values = array_values($config);

        $keys[] = '__ip__';
        $values[] = hostIp();

        $keys[] = '__password__';
        $values[] = \Str::random(100);

        $keys[] = '__url__';
        $values[] = config('app.url');


        return str_replace($keys, $values, $content);
    }
}
