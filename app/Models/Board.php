<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Board extends Model
{
    use HasFactory;

    // Table name (optional if Laravel naming convention is followed)
    protected $table = 'rproviders';

    // Mass assignable fields
    protected $guarded = [];
    
    public function electricity_bill()
    {
        return $this->hasMany(ElectricityBill::class);
    }
}
