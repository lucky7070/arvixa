<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\Retailer;

class RetailerObserver
{
    public function created(Retailer $retailer)
    {
        if (empty($retailer->employee_id)) {
            $employee_id = 0;
            $retailer_id = $retailer->id;
            $empArr = Employee::select('id')->where('status', 1)->where('is_active',)->get()->pluck('id');

            if ($empArr->count() === 0)  $employee_id = 0;
            if ($empArr->count() === 1)  $employee_id = $empArr->first();
            if ($empArr->count() > 1) {

                $lastRetailerId = (int) $retailer_id - 1;
                if ($lastRetailerId <= 0) $employee_id = $empArr->first();
                if ($lastRetailerId > 0) {
                    $employee_id        = $empArr->first();
                    $lastRetailer       = Retailer::select('id', 'employee_id')->with('employee:id')->orderBy('created_at', 'desc')->skip(1)->first();

                    if (!empty($lastRetailer) && $lastRetailer->employee) {
                        $employee_id                    = $empArr->first(fn ($val) => $val > $lastRetailer->employee->id);
                        if (!$employee_id) $employee_id = $empArr->first();
                    }
                }
            }

            // Code after save
            $retailer->employee_id  = $employee_id;
        }

        $retailer->userId       = orderId($retailer->id, 'R', 6);
        $retailer->saveQuietly();
    }
}
