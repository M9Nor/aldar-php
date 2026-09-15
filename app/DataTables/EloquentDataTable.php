<?php

namespace App\DataTables;

use Yajra\DataTables\EloquentDataTable as BaseEloquentDataTable;

/**
 * The Eloquent DataTables engine, registered in config/datatables.php, that processes rows with
 * App\DataTables\DataProcessor so the admin JSON keeps its Laravel 7 value types.
 * Every DataTables::of() call in the app receives an Eloquent builder, so this is the only engine replaced.
 */
class EloquentDataTable extends BaseEloquentDataTable
{
    /**
     * Same as Yajra\DataTables\DataTableAbstract::processResults(), with the app's processor.
     */
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
