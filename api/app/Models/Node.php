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
 * @property string|null $model
 * @property string|null $boot_order
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
 * @method static \Illuminate\Database\Eloquent\Builder|Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereArch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereBootOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereCpuMaxFreq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereCpus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereMac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereNetboot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereNetbooted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereRamMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereStorageDevices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Node extends BaseModel
{
    protected $table = 'nodes';

    public const BOOT_SDCARD_DETECT = '0'; // @depracated by rpi fundation
    public const BOOT_SDCARD = '1';
    public const BOOT_NETWORK = '2';
    public const BOOT_RPIBOOT = '3';
    public const BOOT_USB = '4';
    public const BOOT_BCMUSBMSD = '5';
    public const BOOT_NVME = '6';
    public const BOOT_STOP = 'e';
    public const BOOT_RESTART = 'f';

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
        'model' => 'string',
        'boot_order' => 'string',
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

    /**
     * @param string $configBootOrder
     * @return string
     */
    public static function decodeBootOrder(string $configBootOrder): string
    {
        return implode(
            ',',
            array_reverse(
                str_split(
                    str_replace('0x', '', $configBootOrder)
                )
            )
        );
    }

    /**
     * @param string $bootOrder
     * @return string
     */
    public static function encodeBootOrder(string $bootOrder): string
    {
        return '0x' . implode('', array_reverse(explode(',', $bootOrder)));
    }


    /**
     * @return string[]
     */
    public static function bootMapList(): array
    {
        return [
            static::BOOT_SDCARD_DETECT,
            static::BOOT_SDCARD,
            static::BOOT_NETWORK,
            static::BOOT_RPIBOOT,
            static::BOOT_USB,
            static::BOOT_BCMUSBMSD,
            static::BOOT_NVME,
            static::BOOT_STOP,
            static::BOOT_RESTART,
        ];
    }
}
