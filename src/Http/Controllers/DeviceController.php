<?php

namespace LBF\Devices\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LBF\Devices\Http\Requests\CreateDeviceRequest;
use LBF\Devices\Models\Device;
use LBF\Devices\Http\Resources\DeviceResource;

class DeviceController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('device')->except('store');
    }

    /**
     * Store a newly created device in storage.
     *
     * @param  \LBF\Devices\Http\Requests\CreateDeviceRequest  $request
     *
     * @return \LBF\Devices\Http\Resources\DeviceResource
     */
    public function store(CreateDeviceRequest $request)
    {
        $uuid = $request->input('uuid');

        // Device does not exist, create the device
        if (! $device = Device::deviceByUUID($uuid)->first()) {
            $device = new Device();
            $device->uuid = $uuid;
            $device->platform = $request->input('platform');
            $device->auth_token = str_random(100);
            $device->push_token = $request->input('push_token', null);
            $device->save();
        }

        return new DeviceResource($device);
    }

    /**
     * Display the specified device.
     *
     * @param \Illuminate\Http\Request $request
     * @param \LBF\Devices\Models\Device      $device
     *
     * @return \LBF\Devices\Http\Resources\DeviceResource
     */
    public function show(Request $request, Device $device)
    {
        if (! $device->hasCorrectDeviceIdHeader($request)) {
            abort(401, 'Device ID does not match the id in the URL request');
        }

        return new DeviceResource($device);
    }

    /**
     * Update the specified device in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \LBF\Devices\Models\Device      $device
     *
     * @return \LBF\Devices\Http\Resources\DeviceResource
     */
    public function update(Request $request, Device $device) : DeviceResource
    {
        if (! $device->hasCorrectDeviceIdHeader($request)) {
            abort(401, 'Device ID does not match the id in the URL request');
        }

        $device->firebase_push_token = $request->input('firebase_push_token', null);
        $device->save();

        return new DeviceResource($device);
    }
}
