<?php

namespace App\Repository\Eloquent;

use App\Models\Contact;
use App\Repository\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Contact $model)
    {
        $this->model = $model;
    }
}
