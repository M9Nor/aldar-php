<?php

namespace Modules\Backend\Entities;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $table = 'contact_us';

    protected $fillable = [
        'sender',
        'email',
        'phone',
        'description',
        'link',
        'type_of_visit',
        'residency_address',
        'nationality',
        'native_language',
        'budget',
        'type_of_residency',
        'language',
        'time_from',
        'time_to',
    ];
}