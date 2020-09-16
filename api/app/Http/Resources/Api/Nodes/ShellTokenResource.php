<?php

namespace App\Http\Resources\Api\Nodes;

use App\Models\ShellToken;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShellTokenResource
 *
 * @mixin ShellToken
 */
class ShellTokenResource extends JsonResource
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
            'token' => $this->token
        ];
    }
}
