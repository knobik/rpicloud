<?php

namespace App\Models;

/**
 * App\Models\JobStatus
 *
 * @property int $id
 * @property int $node_id
 * @property int $failed
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereName($value)
 * @property string|null $finished_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobStatus whereFinishedAt($value)
 */
class JobStatus extends BaseModel
{
    protected $table = 'job_status';
}
