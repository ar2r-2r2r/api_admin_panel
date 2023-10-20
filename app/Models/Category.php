<?php

namespace App\Models;

use App\Exceptions\CategoryNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Category extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * @throws CategoryNotFoundException
     */
    public static function findByName($categoryName): Category
    {
        $category = static::where('name', $categoryName)->first();

        if (!$category) {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
