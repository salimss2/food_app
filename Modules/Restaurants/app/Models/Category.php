<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    /**
     * Get the absolute URL for the category image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // Fallback image based on name
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff&size=128';
    }
}
