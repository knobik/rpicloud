<?php

namespace App\Http\Resources\Api\Nodes;

use App\Models\Node;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OperationResource
 *
 * @mixin Operation
 */
class OperationResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'log' => $this->log,
            'failed' => $this->failed,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'started_at' => $this->started_at ? $this->started_at->format('Y-m-d H:i:s') : null,
            'finished_at' => $this->finished_at ? $this->finished_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
