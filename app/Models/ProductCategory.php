<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Kalnoy\Nestedset\NodeTrait;
// use Rennokki\QueryCache\Traits\QueryCacheable;

class ProductCategory extends Model
{
    use HasSlug;
    use NodeTrait;
    // use QueryCacheable;

    // Cache Duration
    // public $cacheFor = 3600;
    // protected static $flushCacheOnUpdate = true;


    protected $table = 'product_category';
    protected $fillable = [
        'name', 'slug', 'parent_id', 'thumbnail', 'cover',
    ];

    protected $appends = [
        'thumbnail_url', 'level'
    ];

    public function child(){
        return $this->hasMany(ProductCategory::class, 'parent_id');

    }

    public function parent(){
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function getLevelAttribute()
    {
        if(empty($this->parent_id)){
            return 0;
        }else{
            $parent = $this->parent;
            if($parent->parent){
                return 2;
            }else{
                return 1;
            }
        }
    }


    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }


    public function getThumbnailUrlAttribute()
    {
        if($this->thumbnail){
            return asset($this->thumbnail);
        }else{
            return null;
        }
    }

    public function getCreatedAtAttribute($value)
	{
		return Carbon::parse($value)->format('d-m-Y');
    }

}
