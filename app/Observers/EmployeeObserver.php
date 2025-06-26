<?php

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver
{
    public function created(Employee $employee)
    {
        // Code after save
        $employee->userId = orderId($employee->id, 'E', 6);
        $employee->saveQuietly();
    }
}
