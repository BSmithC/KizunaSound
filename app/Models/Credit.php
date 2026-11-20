<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'name',
        'credit_details_id',
    ];

    public function details()
    {
        return $this->hasMany(CreditDetail::class, 'credit_details_id');
    }
}
