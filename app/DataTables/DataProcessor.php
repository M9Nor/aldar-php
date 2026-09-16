<?php

namespace App\DataTables;

use Illuminate\Support\Arr;
use Yajra\DataTables\Processors\DataProcessor as BaseDataProcessor;

/**
 * Keeps the Laravel 7 (yajra/laravel-datatables-oracle 9.9.0) escaping of DataTables rows.
 *
 * With `columns.escape` set to '*', yajra 9 passed every non-empty value through e(), so ints,
 * floats and `true` reached the JSON as strings ("id": "12", "DT_RowIndex": "1"). yajra 13 only
 * escapes strings and Htmlable values and leaves the rest as native types.
 *
 * Only App\DataTables\EloquentDataTable uses this processor. A future DataTables::of() on a Collection
 * or a Query builder bypasses it and emits yajra 13 native value types.
 *
 * Removal condition: once the admin JSON consumers no longer need the Laravel 7 string value types and
 * the admin-table-values parity baselines are re-accepted, delete this class together with
 * EloquentDataTable and its config/datatables.php engine entry.
 */
class DataProcessor extends BaseDataProcessor
{
    /**
     * Copy of Yajra\DataTables\Processors\DataProcessor::escapeRow() from v9.9.0.
     */
    protected function escapeRow(array $row): array
    {
        $arrayDot = array_filter(Arr::dot($row));
        foreach ($arrayDot as $key => $value) {
            if (! in_array($key, $this->rawColumns)) {
                $arrayDot[$key] = e($value);
            }
        }

        foreach ($arrayDot as $key => $value) {
            Arr::set($row, $key, $value);
        }

        return $row;
    }
}
