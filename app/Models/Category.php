<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['icon', 'name', 'slug'];

    public function posts()
    {
        return $this->hasMany(post::class);
    }

    public function topics()
    {
        return $this->hasMany(topic::class);
    }
}
