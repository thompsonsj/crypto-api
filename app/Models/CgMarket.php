<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class CgMarket extends Model
{
    protected $connection = 'mongodb';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
