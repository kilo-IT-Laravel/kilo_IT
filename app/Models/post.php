<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'author_id',
        'thumbnail',
        'read_time',
        'published_at',
        'views',
        'likes'
    ];
    protected $dates = ['published_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(categorie::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function views()
    {
        return $this->hasMany(post_view::class);
    }

    public function uploadMedia()
    {
        return $this->hasOne(upload_media::class);
    }

    //filter public post 
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function incrementViews()
    {
        $this->views += 1;
        $this->save();
    }

    // Publish post
    public function publish()
    {
        $this->published_at = now();
        $this->save();
    }

    // Unpublish post
    public function unpublish()
    {
        $this->published_at = null;
        $this->save();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');  
    }
}
