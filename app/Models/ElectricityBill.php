<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectricityBill extends Model
{
    use HasFactory, SoftDeletes;

    // Table name (optional if Laravel naming convention is followed)
    protected $table = 'electricity_bills';

    // Mass assignable fields
    protected $fillable = [
        'transaction_id',
        'user_id',
        'board_id',
        'consumer_no',
        'consumer_name',
        'bill_no',
        'bill_amount',
        'bill_type',
        'due_date',
        'status',
        'is_refunded',
        'bu_code',
        'commission',
        'tds',
        'commission_distributor',
        'tds_distributor',
        'commission_main_distributor',
        'tds_main_distributor',
        'remark'
    ];

    // Cast these fields as dates
    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user associated with the bill.
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'user_id');
    }
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
