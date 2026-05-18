<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Restaurants\Database\Factories\RestaurantFactory;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'status',
        'category',
        'owner_id',
        'user_id',
        'logo',
        'description',
        'phone',
        'account_status',
        'is_open',
        'commission_rate',
    ];

    protected $appends = ['is_open', 'logo_url', 'image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('s3')->url($this->image_path) : null;
    }

    public function getIsOpenAttribute()
    {
        return $this->status === 'open';
    }

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            $path = str_contains($this->logo, '/') ? $this->logo : 'restaurants/logos/' . $this->logo;
            return asset('storage/' . $path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }


    protected $guarded = [];

    public function mealCategories()
    {
        return $this->hasMany(MealCategory::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    public function categories()
    {
        return $this->hasMany(MealCategory::class);
    }

    // Add this to resolve the RelationNotFoundException

    public function meal_categories()
    {
        // تأكد من عمل import لمودل MealCategory في أعلى الملف إذا لزم الأمر
        return $this->hasMany(MealCategory::class, 'restaurant_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\Orders\Models\Order::class, 'restaurant_id');
    }
}
