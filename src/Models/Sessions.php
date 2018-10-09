<?php

namespace LBF\Devices\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'device_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['device_id', 'user_id'];
}
