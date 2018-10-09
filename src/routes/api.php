<?php

Route::apiResource('api/devices', '\LBF\Devices\Http\Controllers\DeviceController')->except(['index', 'destroy']);