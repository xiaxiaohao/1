<?php

namespace App\Imports;



use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class SupplierImport implements ToModel
{

    /**
     * @param array $row
     *
     * @return Model|Model[]|null
     */
    public function model(array $row)
    {
        return new Supplier([

        ]);
    }
}
