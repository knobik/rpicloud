<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Backup
 *
 * @property int $id
 * @property int $node_id
 * @property string $filename
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Node|null $node
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Backup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Backup extends BaseModel
{
    protected $table = 'backups';

    /**
     * @return BelongsTo
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
