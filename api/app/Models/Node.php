<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;

/**
 * App\Models\Node
 *
 * @property int $id
 * @property string $ip
 * @property string|null $hostname
 * @property string|null $mac
 * @property bool $netboot
 * @property bool $netbooted
 * @property bool $netbootable
 * @property bool $online
 * @property string|null $arch
 * @property string|null $model
 * @property string|null $bootloader_timestamp
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
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereBootloaderTimestamp($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereNetbootable($value)
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

    public const BOOT_SDCARD_DETECT = '0'; // @depracated, we dont want to support this.
    public const BOOT_SDCARD = '1';
    public const BOOT_NETWORK = '2';
    public const BOOT_RPIBOOT = '3'; // only CM4 and a special debugging mode, we dont support this.
    public const BOOT_USB = '4';
    public const BOOT_BCMUSBMSD = '5';
    public const BOOT_NVME = '6';
    public const BOOT_STOP = 'e';
    public const BOOT_RESTART = 'f';

    public const VERSION_MAP = [
        'Raspberry Pi 4' => 4,
        'Raspberry Pi 3' => 3,
        'Raspberry Pi 2' => 2,
    ];

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
        'netbootable' => 'bool',
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
     * @return int|null
     */
    public function getVersion(): ?int
    {
        foreach (static::VERSION_MAP as $modelValue => $version) {
            if (str_contains($this->model, $modelValue)) {
                return $version;
            }
        }

        return null;
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
     * @param string|array $bootOrder
     * @return string
     */
    public static function encodeBootOrder(string|array $bootOrder): string
    {
        if (is_array($bootOrder)) {
            $bootOrder = collect($bootOrder)->pluck('id')->join(',');
        }

        return '0x' . implode('', array_reverse(explode(',', $bootOrder)));
    }

    /**
     * @return array
     */
    public function bootOrderList(): array
    {
        return static::listBootStates($this->boot_order ?? '');
    }

    /**
     * @param string $bootOrder
     * @return array
     */
    public static function listBootStates(string $bootOrder): array
    {
        $order = [];
        foreach (explode(',', $bootOrder) as $key) {
            if (!empty($key)) {
                $order[] = [
                    'id' => $key,
                    'name' => static::bootMapList()[$key]
                ];
            }
        }
        return $order;
    }

    /**
     * @return array
     */
    public static function allBootStates(): array
    {
        return static::listBootStates(implode(',', array_keys(static::bootMapList())));
    }

    /**
     * @return string[]
     */
    public static function bootMapList(): array
    {
        return [
            static::BOOT_SDCARD => 'SD Card',
            static::BOOT_NETWORK => 'Network',
            static::BOOT_USB => 'USB',
            static::BOOT_BCMUSBMSD => 'BCM USB',
            static::BOOT_NVME => 'NVME',
            static::BOOT_STOP => 'Stop',
            static::BOOT_RESTART => 'Restart',
        ];
    }
}
