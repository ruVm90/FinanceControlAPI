<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $description
 * @property float $amount
 * @property int $user_id
 * @property int $category_id
 * @property-read \App\Models\category $category
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|expense whereUserId($value)
 * @mixin \Eloquent
 */
class expense extends Model
{
    protected $fillable = [
        'title',
        'description',
        'amount',
        'category_id',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
