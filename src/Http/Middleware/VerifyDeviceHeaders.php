<?php

namespace LBF\Devices\Http\Middleware;

use Closure;
use LBF\Devices\Models\Device;

class VerifyDeviceHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $deviceId = $request->headers->get('Device-ID');
        $deviceToken = $request->headers->get('Device-Token');
        $device = Device::find($deviceId);

        // Check if device token exist in request
        if (! $deviceId || ! $deviceToken) {
            abort(401, 'Please provide a Device-ID and Device-Token to access this resource.');
        }

        // Check if the device exist - did we find it in the database
        if (! $device) {
            abort(404, 'Device does not exist.');
        }

        // Check if deviceId matches the deviceToken provided
        if ($device->auth_token != $deviceToken) {
            abort(401, 'Device does not match the Device-Token provided');
        }

        return $next($request);
    }
}
