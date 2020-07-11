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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereName($value)
 * @property string|null $finished_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereFinishedAt($value)
 * @property string|null $log
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereLog($value)
 * @property string|null $started_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Operation whereStartedAt($value)
 */
class Operation extends BaseModel
{
    protected $table = 'operations';

    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'log' => 'string',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'started_at',
        'finished_at',
    ];
}
