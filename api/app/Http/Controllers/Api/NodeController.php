<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\NodeResource;
use App\Models\Node;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NodeController extends ApiController
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return NodeResource::collection(Node::all());
    }

    /**
     * @param  Node  $node
     * @return NodeResource
     */
    public function show(Node $node): NodeResource
    {
        return new NodeResource($node);
    }
}
