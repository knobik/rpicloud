<?php

namespace App\Models;

/**
 * App\Models\Node
 *
 * @property int $id
 * @property string $mac
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Node extends BaseModel
{

}
