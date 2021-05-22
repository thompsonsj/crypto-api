<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class CgExchange extends Model
{
    protected $connection = 'mongodb';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
