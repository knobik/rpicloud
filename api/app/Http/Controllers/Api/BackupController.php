<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Backups\RestoreRequest;
use App\Http\Requests\Api\Backups\UploadRequest;
use App\Http\Resources\Api\NodeResource;
use App\Http\Resources\Api\Nodes\BackupResource;
use App\Models\Backup;
use App\Models\Node;
use App\Operations\RestoreOperation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class BackupController extends ApiController
{
    public const BACKUPS_DIRECTORY = "/nfs/backups";

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
     * @param UploadRequest $request
     * @return BackupResource
     * @throws ValidationException
     */
    public function upload(UploadRequest $request): BackupResource
    {
        // this code is ugly as form-data returns string 'null' instead of nothing.
        $file = $request->file('file');
        if ($file === 'null') {
            $file = null;
        }

        $url = $request->get('url');
        if ($url === 'null') {
            $url = null;
        }

        if ($file) {
            $this->validate($request, [
                'file' => 'file'
            ]);
        }

        if ($url) {
            $this->validate($request, [
                'url' => 'url|ends_with:.img'
            ]);

            set_time_limit(0);
            $tmpPath = '/tmp/' . basename($url);
            $process = (new Process(['curl', '-fSL', $url, '-o', $tmpPath]));
            $process->run();

            if (!file_exists($tmpPath)) {
                throw ValidationException::withMessages([
                    'url' => trans('validation.active_url', ['attribute' => 'url'])
                ]);
            }

            $file = new UploadedFile($tmpPath, basename($url));
        }

        // nothing provided, we drop this request.
        if (!$file) {
            throw ValidationException::withMessages([
                'file' => trans('validation.required', ['attribute' => 'file'])
            ]);
        }

        $path = static::BACKUPS_DIRECTORY . "/{$file->getClientOriginalName()}";
        rename($file->getPathname(), $path);
        (new Process(['sudo', 'chown', 'root:root', $path]))->run();

        $model = Backup::firstOrCreate(
            [
                'filename' => $file->getClientOriginalName()
            ]
        );

        return new BackupResource($model);
    }

    /**
     * @param Backup $backup
     * @throws \Exception
     */
    public function destroy(Backup $backup)
    {
        $path = static::BACKUPS_DIRECTORY . "/{$backup->filename}";
        if (file_exists($path)) {
            unlink($path);
        }

        $backup->delete();
    }
}
