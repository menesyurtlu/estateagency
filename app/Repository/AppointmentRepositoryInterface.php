<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface extends EloquentRepositoryInterface {
    // Get all appointments by User ID
    public function allByUser($userId): Collection;
}
