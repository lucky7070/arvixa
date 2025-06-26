<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends Authenticatable
{
    use  HasFactory, SoftDeletes;
    protected $fillable = [
        'slug',
        'name',
        'status',
    ];

    public function permission()
    {
        return $this->hasMany(RolePermission::class)->with('permission_name');
    }
}
