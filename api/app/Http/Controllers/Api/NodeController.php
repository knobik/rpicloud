<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PXEException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Nodes\BackupRequest;
use App\Http\Resources\Api\NodeResource;
use App\Models\Node;
use App\Operations\BackupOperation;
use App\Operations\RebootOperation;
use App\Operations\ShutdownOperation;
use App\Services\PXEService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NodeController extends ApiController
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return NodeResource::collection(Node::with(['pendingOperations'])->get());
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function show(Node $node): NodeResource
    {
        return new NodeResource($node->load(['pendingOperations']));
    }

    /**
     * @param PXEService $PXEService
     * @param Node $node
     * @return NodeResource
     * @throws PXEException
     */
    public function enableNetboot(PXEService $PXEService, Node $node): NodeResource
    {
        $PXEService->enableNetboot($node);

        return new NodeResource($node->load(['pendingOperations']));
    }

    /**
     * @param PXEService $PXEService
     * @param Node $node
     * @return NodeResource
     * @throws PXEException
     */
    public function disableNetboot(PXEService $PXEService, Node $node): NodeResource
    {
        $PXEService->disableNetboot($node);

        return new NodeResource($node->load(['pendingOperations']));
    }

    /**
     * @param BackupRequest $request
     * @param Node $node
     * @return NodeResource
     */
    public function backup(BackupRequest $request, Node $node)
    {
        $filename = PXEService::slug($node) . '_' . now()->format('Y-m-d_H-i-s') . '.img';
        (new BackupOperation($node, $request->get('storageDevice'), $filename))->dispatch();

        return new NodeResource($node->load(['pendingOperations']));
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function reboot(Node $node): NodeResource
    {
        (new RebootOperation($node))->dispatch();

        return new NodeResource($node->load(['pendingOperations']));
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function shutdown(Node $node): NodeResource
    {
        (new ShutdownOperation($node))->dispatch();

        return new NodeResource($node->load(['pendingOperations']));
    }
}
