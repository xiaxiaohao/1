<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;

class Import implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return null;
    }
}
