<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Nodes\OperationResource;
use App\Models\Node;
use App\Models\Operation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OperationController extends ApiController
{
    /**
     * @param Node $node
     * @return AnonymousResourceCollection
     */
    public function index(Node $node): AnonymousResourceCollection
    {
        return OperationResource::collection(
            Operation::where('node_id', '=', $node->id)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * @param Operation $operation
     * @return OperationResource
     */
    public function show(Operation $operation): OperationResource
    {
        return new OperationResource($operation);
    }
}
