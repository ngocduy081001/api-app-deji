<?php

namespace Vendor\Customer\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'phone',

    ];
}
