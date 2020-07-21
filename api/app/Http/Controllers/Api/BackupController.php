<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Backups\RestoreRequest;
use App\Http\Resources\Api\NodeResource;
use App\Http\Resources\Api\Nodes\BackupResource;
use App\Models\Backup;
use App\Models\Node;
use App\Operations\RestoreOperation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BackupController extends ApiController
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Backup::with(['node']);

        if ($nodeId = $request->get('nodeId')) {
            $query->where('node_id', '=', $nodeId);
        }

        $query->orderBy('created_at', 'desc');

        return BackupResource::collection(
            $query->get()
        );
    }

    /**
     * @param RestoreRequest $request
     * @param Backup $backup
     * @return NodeResource
     */
    public function restore(RestoreRequest $request, Backup $backup): NodeResource
    {
        $node = Node::findOrFail($request->get('nodeId'));
        (new RestoreOperation(
            $node,
            $request->get('storageDevice'),
            $backup->filename,
            $request->get('hostname')
        ))->dispatch();

        return new NodeResource($node);
    }

    /**
     * @param Backup $backup
     * @throws \Exception
     */
    public function destroy(Backup $backup)
    {
        $path = "/nfs/backups/{$backup->filename}";
        if (file_exists($path)) {
            unlink($path);
        }

        $backup->delete();
    }
}