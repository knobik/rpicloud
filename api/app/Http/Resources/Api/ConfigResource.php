<?php

namespace App\Http\Resources\Api;

use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ConfigResource
 */
class ConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'url' => config('app.url'),
            'bootStates' => Node::allBootStates(),
            'requiredBootloaderTimestamp' => 1599177600 // 2020-09-04 00:00:00 https://www.raspberrypi.com/documentation/computers/raspberry-pi.html#BOOT_ORDER
        ];
    }
}
