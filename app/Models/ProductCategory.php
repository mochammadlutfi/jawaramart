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
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // public function getThumbnailAttribute()
    // {
    //     if( $this->attributes['thumbnail'] !== null){
    //         return $this->attributes['thumbnail'];
    //     }else{
    //         return 'media/placeholder/category.png';
    //     }
    // }

    public function getThumbnailUrlAttribute()
    {
        if(file_exists( public_path() . '/' . $this->thumbnail) && $this->thumbnail !== null){
            return asset($this->thumbnail);
        }

        return asset('media/placeholder/category.png');
    }

    public function getCreatedAtAttribute($value)
	{
		return Carbon::parse($value)->format('d-m-Y');
    }

}
