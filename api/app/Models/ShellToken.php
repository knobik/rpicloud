<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ShellToken
 *
 * @property int $id
 * @property int $user_id
 * @property int $node_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Node $node
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShellToken whereUserId($value)
 * @mixin \Eloquent
 */
class ShellToken extends BaseModel
{
    protected $table = 'shell_tokens';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'node_id' => 'int',
        'token' => 'string',
    ];

    /**
     * @return BelongsTo
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
