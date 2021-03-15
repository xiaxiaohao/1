<?php

namespace App\Imports;

use App\Models\Procurement;
use Maatwebsite\Excel\Concerns\ToModel;

class ProcurementImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Procurement([

        ]);
    }
}
