<?php

namespace App\DataTables;

use Yajra\DataTables\EloquentDataTable as BaseEloquentDataTable;

/**
 * The Eloquent DataTables engine, registered in config/datatables.php, that processes rows with
 * App\DataTables\DataProcessor so the admin JSON keeps its Laravel 7 value types.
 * Every DataTables::of() call in the app receives an Eloquent builder, so this is the only engine replaced.
 *
 * Non-Eloquent sources are not covered: a future DataTables::of() on a Collection or a Query builder
 * resolves yajra's CollectionDataTable or QueryDataTable, bypasses this processor, and emits yajra 13
 * native value types (ints, floats, booleans) instead of the Laravel 7 strings.
 *
 * Removal: this class, DataProcessor and the config/datatables.php engine entry can go once the admin
 * JSON consumers no longer need the Laravel 7 string value types and the admin-table-values parity
 * baselines (tests/e2e/snapshots/parity/admin-table-values.spec.ts) have been re-accepted without them.
 */
class EloquentDataTable extends BaseEloquentDataTable
{
    /**
     * Same as Yajra\DataTables\DataTableAbstract::processResults(), with the app's processor.
     */
    #[\Override]
    protected function processResults($results, $object = false): array
    {
        $processor = new DataProcessor(
            $results,
            $this->getColumnsDefinition(),
            $this->templates,
            $this->request->start()
        );

        if ($this->processCallback) {
            $processor->processWith($this->processCallback);
        }

        return $processor->process($object);
    }
}
