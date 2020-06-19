<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PXEException;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\NodeResource;
use App\Models\Node;
use App\Services\PXEService;
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

    /**
     * @param  PXEService  $PXEService
     * @param  Node  $node
     * @return NodeResource
     * @throws PXEException
     */
    public function enableNetboot(PXEService $PXEService, Node $node): NodeResource
    {
        $PXEService->enableNetboot($node);

        return new NodeResource($node);
    }

    /**
     * @param  PXEService  $PXEService
     * @param  Node  $node
     * @return NodeResource
     * @throws PXEException
     */
    public function disableNetboot(PXEService $PXEService, Node $node): NodeResource
    {
        $PXEService->disableNetboot($node);

        return new NodeResource($node);
    }
}
