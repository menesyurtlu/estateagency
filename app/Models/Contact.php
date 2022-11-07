<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "surname",
        "email",
        "phone"
    ];

    public function appointments() {
        return $this->hasMany(Appointment::class, 'contact_id', 'id');
    }
}
