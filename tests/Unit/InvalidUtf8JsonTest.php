<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Modules\Cms\Classes\ResponseHandler;
use Tests\TestCase;

/** S12: JSON answers substitute U+FFFD for invalid UTF-8 instead of failing, and valid output is unchanged. */
class InvalidUtf8JsonTest extends TestCase
{
    private function ajaxRequest(): Request
    {
        return Request::create('/', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
    }

    public function test_an_ajax_response_handler_substitutes_invalid_utf8(): void
    {
        $response = (new ResponseHandler(['description' => "probe \xED\xA0\xBD\xED\xB8\x80 end"]))->toResponse($this->ajaxRequest());

        $decoded = json_decode($response->getContent(), true);
        $this->assertIsArray($decoded);
        $this->assertStringStartsWith('probe ', $decoded['description']);
        $this->assertStringContainsString("\u{FFFD}", $decoded['description']);
    }

    public function test_valid_utf8_encodes_exactly_as_before(): void
    {
        $data = ['title' => 'عقارات "Aldar" & <b>/path</b>', 'count' => 3];

        $this->assertSame(json_encode($data), (new ResponseHandler($data))->toResponse($this->ajaxRequest())->getContent());
    }

    public function test_datatables_json_substitutes_invalid_utf8(): void
    {
        $this->assertSame(JSON_INVALID_UTF8_SUBSTITUTE, config('datatables.json.options') & JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
