<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'txn_id';

    protected $fillable = [
        'user_id',
        'date',
        'type_id',
        'category_id',
        'amount',
    ];

    public $timestamps = true;
}
