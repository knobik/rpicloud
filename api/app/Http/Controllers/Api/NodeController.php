<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PXEException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Nodes\BackupRequest;
use App\Http\Requests\Api\Nodes\BootOrderRequest;
use App\Http\Requests\Api\Nodes\BulkRequest;
use App\Http\Resources\Api\NodeResource;
use App\Http\Resources\Api\Nodes\ShellTokenResource;
use App\Models\Node;
use App\Models\ShellToken;
use App\Operations\BackupOperation;
use App\Operations\RebootOperation;
use App\Operations\SetBootOrderOperation;
use App\Operations\ShutdownOperation;
use App\Services\PXEService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NodeController extends ApiController
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Node::query()->with('pendingOperations');

        if ($request->get('online')) {
            $query->where('online', '=', (bool)$request->get('online'));
        }

        return NodeResource::collection($query->get());
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function show(Node $node): NodeResource
    {
        return new NodeResource($node);
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

        return new NodeResource($node);
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

        return new NodeResource($node);
    }

    /**
     * @param BackupRequest $request
     * @param Node $node
     * @return NodeResource
     */
    public function backup(BackupRequest $request, Node $node): NodeResource
    {
        $filename = PXEService::slug($node) . '_' . now()->format('Y-m-d_H-i-s') . '.img';
        (new BackupOperation($node, $request->get('storageDevice'), $filename))->dispatch();

        return new NodeResource($node);
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function reboot(Node $node): NodeResource
    {
        (new RebootOperation($node))->dispatch();

        return new NodeResource($node);
    }

    /**
     * @param BulkRequest $request
     * @return Collection
     */
    public function bulkReboot(BulkRequest $request): Collection
    {
        $resources = collect();
        foreach ($request->get('nodeId', []) as $nodeId) {
            $resources[] = $this->reboot(Node::findOrFail($nodeId));
        }

        return $resources;
    }

    /**
     * @param Node $node
     * @return NodeResource
     */
    public function shutdown(Node $node): NodeResource
    {
        (new ShutdownOperation($node))->dispatch();

        return new NodeResource($node);
    }

    /**
     * @param BulkRequest $request
     * @return Collection
     */
    public function bulkShutdown(BulkRequest $request): Collection
    {
        $resources = collect();
        foreach ($request->get('nodeId', []) as $nodeId) {
            $resources[] = $this->shutdown(Node::findOrFail($nodeId));
        }

        return $resources;
    }

    /**
     * @param Node $node
     * @return ShellTokenResource
     */
    public function shellAccess(Node $node): ShellTokenResource
    {
        return new ShellTokenResource(
            ShellToken::create(
                [
                    'user_id' => auth()->user()->id,
                    'node_id' => $node->id,
                    'token' => Str::random(32)
                ]
            )
        );
    }

    /**
     * @param Node $node
     * @param BootOrderRequest $request
     * @return NodeResource
     */
    public function updateBootOrder(Node $node, BootOrderRequest $request): NodeResource
    {
        $operation = new SetBootOrderOperation(
            $node,
            $request->get('bootOrder'),
            (bool)$request->get('includeDhcpOption')
        );
        $operation->dispatch();

        return new NodeResource($node);
    }
}
