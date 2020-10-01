<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Node
 *
 * @property int $id
 * @property string $ip
 * @property string|null $hostname
 * @property string|null $mac
 * @property bool $netboot
 * @property bool $netbooted
 * @property bool $online
 * @property string|null $arch
 * @property int|null $cpus
 * @property int|null $cpu_max_freq
 * @property int|null $ram_max
 * @property array $storage_devices
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Operation[] $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Operation[] $pendingOperations
 * @property-read int|null $pending_operations_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereArch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereCpuMaxFreq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereCpus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereNetboot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereNetbooted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereRamMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereStorageDevices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Node whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Node extends BaseModel
{
    protected $table = 'nodes';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ip' => 'string',
        'mac' => 'string',
        'netboot' => 'bool',
        'hostname' => 'string',
        'netbooted' => 'bool',
        'online' => 'bool',
        'storage_devices' => 'array'
    ];

    /**
     * @return HasMany
     */
    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    /**
     * @return HasMany
     */
    public function pendingOperations(): HasMany
    {
        return $this->operations()->whereNull('finished_at');
    }

    /**
     * @return bool
     */
    public function isNetbooted(): bool
    {
        return $this->online && $this->netboot && $this->netbooted;
    }
}
