<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Restaurants\Models\MealCategory;

class Restaurant extends Model
{
    protected $appends = ['image_url', 'logo_full_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        $diskName = config('filesystems.default', 'public');
        if ($diskName === 's3' && empty(config('filesystems.disks.s3.bucket'))) {
            $diskName = 'public';
        }

        return \Illuminate\Support\Facades\Storage::disk($diskName)->url($this->image_path);
    }

    protected function logoFullUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (!$this->logo) {
                    return null;
                }
                if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                    return $this->logo;
                }
                $diskName = config('filesystems.default', 'public');
                if ($diskName === 's3' && empty(config('filesystems.disks.s3.bucket'))) {
                    $diskName = 'public';
                }
                return \Illuminate\Support\Facades\Storage::disk($diskName)->url($this->logo);
            }
        );
    }

    protected $fillable = [
        'name',
        'logo',
        'image_path',
        'description',
        'address',
        'phone',
        'status',
        'rating'
    ];

    public function meal_categories()
    {
        return $this->hasMany(MealCategory::class);
    }
}