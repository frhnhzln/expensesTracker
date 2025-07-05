<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'types';
    protected $primaryKey = 'type_id';
    public $timestamps = false; // if no created_at / updated_at
    protected $fillable = ['name'];
}
