<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CheckUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $table = 'users';
    protected $ignoreId =  null;
    protected $ignoreColumn = 'id';

    public function __construct($table = 'users',  $ignoreId =  null, $ignoreColumn =  'id')
    {
        $this->table            = $table;
        $this->ignoreId         = $ignoreId;
        $this->ignoreColumn     = $ignoreColumn;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check Unique in main_distributors, distributors, retailers and users table 
        // $a = DB::table('main_distributors')->select('name')->where($attribute, $value);
        // $b = DB::table('distributors')->select('name')->where($attribute, $value);
        // $c = DB::table('retailers')->select('name')->where($attribute, $value);
        // $d = DB::table('users')->select('name')->where($attribute, $value);

        // if ($this->ignoreId) {
        //     if ($this->table  == 'main_distributors')   $a = $a->where($this->ignoreColumn, '!=', $this->ignoreId);
        //     if ($this->table  == 'distributors')        $b = $b->where($this->ignoreColumn, '!=', $this->ignoreId);
        //     if ($this->table  == 'retailers')           $c = $c->where($this->ignoreColumn, '!=', $this->ignoreId);
        //     if ($this->table  == 'users')               $d = $d->where($this->ignoreColumn, '!=', $this->ignoreId);
        // }

        // $count = $d->union($a)->union($b)->union($c)->count();
        // return $count == 0;


        // Check Only In Respective table
        $user = DB::table($this->table)->where($attribute, $value);
        if ($this->ignoreId) $user = $user->where($this->ignoreColumn, '!=', $this->ignoreId);
        $count = $user->count();
        return $count == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute already in use.';
    }
}
