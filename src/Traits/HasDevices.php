<?php

namespace LBF\Devices\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LBF\Devices\Models\Device;
use LBF\Devices\Models\Sessions;

trait HasDevices
{
    /**
     * Gets all devices belonging to the connected user model
     *
     * @return BelongsToMany
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_sessions', 'user_id', 'device_id');
    }

    /**
     * Created a device session / link
     *
     * @param string $userId
     * @param string $deviceId
     *
     * @return \LBF\Devices\Models\Sessions
     */
    public function linkDevice($userId = null, $deviceId = null)
    {
        $deviceId = $this->getDeviceId($deviceId);
        $userId = $this->getUserId($userId);

        return Sessions::firstOrCreate(
            ['user_id' => $userId, 'device_id' => $deviceId]
        );
    }

    /**
     * Destroy a device session / link
     *
     * @param string $userId
     * @param string $deviceId
     *
     * @return void
     */
    public function revokeDevice($userId = null, $deviceId = null)
    {
        $deviceId = $this->getDeviceId($deviceId);
        $userId = $this->getUserId($userId);

        $session = Sessions::where('user_id', $userId)->where('device_id', $deviceId)->first();

        /* If there is no device - user session then the user can't be unlinked from device */
        if (!is_null($session)) {
            $session->delete();
        }
    }

    /**
     * Resolves the deviceId by taking a default parameter or checking the headers.
     *
     * @param string $deviceId
     *
     * @return string
     */
    private function getDeviceId($deviceId = null) {
        if(!$deviceId = $deviceId || request()->headers->get('Device-ID'))
            abort(404, 'Device-ID does not exist. Are you sending Device-ID in your request header?');

        return $deviceId;
    }

    /**
     * Resolves the userId by taking a default parameter or checking this modal.
     *
     * @param string $userId
     *
     * @return string
     */
    private function getUserId($userId = null) {
        if(!$userId = $userId || $this->id)
            abort(404, 'User does not exist. You can manually pass the users ID as the first parameter.');

        return $userId;
    }
}