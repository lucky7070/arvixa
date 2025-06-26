<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'employee_id',
        'message',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
}
