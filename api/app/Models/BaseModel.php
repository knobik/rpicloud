<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BaseModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BaseModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BaseModel withoutTrashed()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];
}
