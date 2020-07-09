<?php

namespace App\Http\Resources\Api;

use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ListResource
 *
 * @mixin Node
 */
class NodeResource extends JsonResource
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
            'id' => $this->id,
            'ip' => $this->ip,
            'hostname' => $this->hostname,
            'mac' => $this->mac,
            'netboot' => $this->netboot,
            'online' => $this->online,
        ];
    }
}
