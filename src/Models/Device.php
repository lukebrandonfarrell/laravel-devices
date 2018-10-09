<?php

namespace LBF\Devices\Models;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Device extends Model
{
    const TABLE = 'devices';
    const PLATFORM_IOS = 1;
    const PLATFORM_ANDROID = 2;
    const PLATFORM_WEB = 3;

    /**
     * The valid set of platform values.
     *
     * @var array
     */
    protected static $platforms = [
        self::PLATFORM_IOS,
        self::PLATFORM_ANDROID,
        self::PLATFORM_WEB,
    ];

    /**
     * The set of aliases for platforms.
     *
     * @var array
     */
    protected static $platformAliases = [
        'web' => self::PLATFORM_WEB,
        'ios' => self::PLATFORM_IOS,
        'apple' => self::PLATFORM_IOS,
        'iphone' => self::PLATFORM_IOS,
        'ipad' => self::PLATFORM_IOS,
        'apns' => self::PLATFORM_IOS,
        'android' => self::PLATFORM_ANDROID,
        'google' => self::PLATFORM_ANDROID,
        'gcm' => self::PLATFORM_ANDROID,
        'fcm' => self::PLATFORM_ANDROID
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'device_sessions', 'device_id', 'user_id');
    }

    /**
     * Tell if this device has a push token.
     *
     * @return bool
     */
    public function hasPushToken()
    {
        return !empty($this->push_token);
    }

    /**
     * Query scope to find device with specific UUID. Since only one UUID can be assigned to a device, one device or
     * none will be returned.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $uuid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeviceByUUID(Builder $query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Checks if device->id is equal to the requests header Device-ID
     *
     * @param \Illuminate\Http\Request;         $request
     *
     * @return bool
     */
    public function hasCorrectDeviceIdHeader($request)
    {
        return $this->id == $request->header('Device-Id');
    }

    /**
     * Scopes the device by UUID. Since only one UUID can be assigned to a device, one device or none will be returned.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $platform
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeviceByPlatform(Builder $query, $platform)
    {
        return $query->where('platform', $platform);
    }


    /**
     * Set this device's platform.
     *
     * @param string|int $platform
     */
    public function setPlatform($platform)
    {
        if (self::isPlatformValue($platform)) {
            $this->attributes['platform'] = $platform;
        } elseif (self::isPlatformAlias($platform)) {
            $this->attributes['platform'] = self::getPlatformValueFromAlias($platform);
        } else {
            throw new InvalidArgumentException('The provided platform value or alias is invalid.');
        }
    }

    /**
     * Mutator to set the platform attribute for this device.
     *
     * @param string|int $platform
     */
    public function setPlatformAttribute($platform)
    {
        $this->setPlatform($platform);
    }

    /**
     * Get a string based upon the stored platform that refers to a "platform" in the SNS package so we are able to
     * subscribe a device to the relevant platform ARN.
     *
     * @return string
     */
    public function getPlatformString()
    {
        if ($this->platform == self::PLATFORM_IOS) {
            return 'ios';
        } elseif ($this->platform == self::PLATFORM_ANDROID) {
            return 'android';
        }

        throw new InvalidArgumentException('Invalid platform stored against device.');
    }

    /**
     * Tell if the given value is a valid platform value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isPlatformValue($value)
    {
        return in_array($value, self::$platforms);
    }

    /**
     * Tell if the given value is a valid platform alias.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isPlatformAlias($value)
    {
        return array_key_exists(self::normalizePlatformAlias($value), self::$platformAliases);
    }

    /**
     * Tell if the given value is a platform value or valid platform alias.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isPlatformValueOrAlias($value)
    {
        return self::isPlatformValue($value) || self::isPlatformAlias($value);
    }

    /**
     * Get a platform value given a valid platform alias.
     *
     * @param string $alias
     *
     * @return bool
     */
    public static function getPlatformValueFromAlias($alias)
    {
        if (self::isPlatformAlias($alias)) {
            return self::$platformAliases[self::normalizePlatformAlias($alias)];
        }

        throw new InvalidArgumentException('The provided platform alias is invalid.');
    }

    /**
     * Gets all the valid platform aliases
     *
     * @return array
     */
    public static function getPlatformAliases()
    {
        return array_keys(self::$platformAliases);
    }

    /**
     * Normalize the given platform alias by converting it to lowercase and trimming it.
     *
     * @param string $alias
     *
     * @return mixed|string
     */
    public static function normalizePlatformAlias($alias)
    {
        return mb_strtolower(trim($alias));
    }
}
