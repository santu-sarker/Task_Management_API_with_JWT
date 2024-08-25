<?php

namespace App\Models;

use App\Helpers\CategoryHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Get the tasks associated with the category.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected static function boot()
    {
        parent::boot();

        // creating & updating the slug  for category
        static::creating(function ($ctg) {
            $ctg->slug = CategoryHelpers::generateSlug($ctg->name);
        });
        // updating the slug if name field  changes
        static::saving(function ($ctg) {
            if ($ctg->isDirty('name')) {
                $ctg->slug = CategoryHelpers::generateSlug($ctg->name);
            }
        });
    }

    /**
     * Generate a slug from the given name.
     *
     * @param string $name
     * @return string
     */
    protected static function generateSlug($name)
    {
        return Str::slug($name, '-');
    }
}
