<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'user_id'
    ];

    public function expenses(){
        return $this->hasMany(Expense::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
