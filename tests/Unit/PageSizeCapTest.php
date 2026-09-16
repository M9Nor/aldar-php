<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Modules\Cms\Classes\PageSize;
use Tests\TestCase;
use Yajra\DataTables\Utilities\Request as DataTablesRequest;

/** S1: request-controlled page sizes are capped at the largest size the UI uses. */
class PageSizeCapTest extends TestCase
{
    public function test_select2_page_sizes_are_capped_at_twenty(): void
    {
        $size = fn (array $query) => PageSize::fromRequest(Request::create('/', 'GET', $query));

        $this->assertSame(20, $size(['items_per_page' => '999999']));
        $this->assertSame(20, $size(['items_per_page' => '20']));
        $this->assertSame(7, $size(['items_per_page' => '7']));
        $this->assertNull($size([]));
        $this->assertNull($size(['items_per_page' => '0']));
        $this->assertNull($size(['items_per_page' => '-3']));
        $this->assertNull($size(['items_per_page' => 'abc']));
        $this->assertNull($size(['items_per_page' => ['20']]));
    }

    public function test_datatables_lengths_are_capped_at_five_hundred(): void
    {
        $this->assertSame(500, config('datatables.max_length'));

        foreach ([['100000', 500], ['-1', 500], ['500', 500], ['50', 50]] as [$asked, $served]) {
            request()->merge(['start' => '0', 'length' => $asked]);
            $this->assertSame($served, (new DataTablesRequest)->length(), "length={$asked}");
        }
    }

    public function test_the_lead_and_property_form_lists_keep_their_all_option(): void
    {
        foreach (['Project', 'Opportunity'] as $name) {
            $source = file_get_contents(base_path("Modules/Backend/Http/Controllers/Admin/{$name}Controller.php"));
            $this->assertSame(2, substr_count($source, 'DataTables::of($list)->ignoreMaxLength()'), "{$name}Controller: data_requests and data_properties");
        }
    }
}
