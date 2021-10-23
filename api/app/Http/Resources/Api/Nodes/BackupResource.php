<?php

namespace App\Http\Resources\Api\Nodes;

use App\Http\Resources\Api\NodeResource;
use App\Models\Backup;
use App\Models\Node;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BackupResource
 *
 * @mixin Backup
 */
class BackupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'node' => new NodeResource($this->node),
            'filename' => $this->filename,
            'filesize' => $this->filesize,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
