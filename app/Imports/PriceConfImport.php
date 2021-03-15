<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class PriceConfImport implements ToCollection
{

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        return $array;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        return $collection;
        // TODO: Implement collection() method.
    }
}
