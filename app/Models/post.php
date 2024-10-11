<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'description', 'category_id', 'author_id', 'thumbnail', 'read_time', 'published_at'];
    protected $dates = ['published_at'];

    public function category()
    {
        return $this->belongsTo(categorie::class);
    }

    public function views()
    {
        return $this->hasMany(post_view::class);
    }

    public function uploadMedia()
    {
        return $this->hasOne(upload_media::class);
    }
}
