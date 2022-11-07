<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $with = [
        "user",
        "contact"
    ];

    protected $fillable = [
        "address",
        "trip_distance",
        "trip_duration",
        "user_id",
        "contact_id",
        "datetime",
        "estimated_departure",
        "estimated_arrival_to_office",
        "status"
    ];

    // Set custom casts for model
    protected $casts = [
        'datetime' => 'datetime:d-m-Y H:i:s',
        'estimated_departure' => 'datetime:d-m-Y H:i:s',
        'estimated_arrival' => 'datetime:d-m-Y H:i:s',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'status' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
