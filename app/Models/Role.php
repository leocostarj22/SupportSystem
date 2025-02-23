<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    // Add this relationship method
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    
    // Update or add this method
    public function hasPermission($permission)
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }
}